<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Ingredient;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Quantity;
use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase management')->only(['index']);
        $this->middleware('permission:purchase add')->only(['store', 'generatePoNo']);
        $this->middleware('permission:purchase edit')->only(['editData', 'update']);
        $this->middleware('permission:purchase delete')->only(['destroy']);
    }

    public function index()
    {
        $purchases = Purchase::with(['store', 'supplier', 'details.item', 'details.ingredient', 'details.food', 'details.unit'])
            ->orderBy('id', 'desc')
            ->get();

        $stores = Store::where('status', 'A')->orWhere('status', '1')->orderBy('store', 'asc')->get();
        if ($stores->isEmpty()) {
            $stores = Store::orderBy('store', 'asc')->get();
        }

        $suppliers = Supplier::where('status', 'A')->orWhere('status', '1')->orderBy('name')->get();
        if ($suppliers->isEmpty()) {
            $suppliers = Supplier::orderBy('name')->get();
        }

        $items = Item::with('unit')->where('status', 'A')->orWhere('status', '1')->orderBy('name')->get();
        $ingredients = Ingredient::with(['buyingUnit', 'usageUnit'])->where('status', 'A')->orWhere('status', '1')->orderBy('name')->get();
        $foods = Food::where('status', 'A')->orWhere('status', '1')->orderBy('name')->get();

        $nextPoNo = $this->getAutoPoNo();

        return view('admin.purchases.index', compact('purchases', 'stores', 'suppliers', 'items', 'ingredients', 'foods', 'nextPoNo'));
    }

    public function generatePoNo()
    {
        return response()->json([
            'status' => true,
            'po_no' => $this->getAutoPoNo()
        ]);
    }

    private function getAutoPoNo()
    {
        $prefix = 'PO-' . date('Ymd') . '-';
        $latest = Purchase::where('po_no', 'LIKE', $prefix . '%')->orderBy('id', 'desc')->first();
        if ($latest && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $latest->po_no, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        } else {
            $nextNum = 1;
        }
        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:cafe_stores,id',
            'supplier_id' => 'required|exists:cafe_suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:item,ingredient,food',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            foreach ($request->items as $row) {
                $qty = floatval($row['quantity'] ?? 0);
                $price = floatval($row['price'] ?? 0);
                $totalAmount += ($qty * $price);
            }

            $poNo = $request->po_no;
            if (empty($poNo)) {
                $poNo = $this->getAutoPoNo();
            }

            $purchase = Purchase::create([
                'store_id' => $request->store_id,
                'supplier_id' => $request->supplier_id,
                'po_no' => $poNo,
                'total' => $totalAmount,
                'purchase_date' => $request->purchase_date,
                'school_id' => $request->school_id ?? null,
                'notes' => $request->notes ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $row) {
                $type = $row['type'];
                $productId = $row['product_id'];
                $unitId = !empty($row['unit_id']) ? $row['unit_id'] : null;
                $qty = floatval($row['quantity']);
                $price = floatval($row['price']);
                $lineTotal = round($qty * $price, 2);

                $detailData = [
                    'purchase_id' => $purchase->id,
                    'item_id' => null,
                    'ingredient_id' => null,
                    'food_id' => null,
                    'unit_id' => $unitId,
                    'quantity' => $qty,
                    'price' => $price,
                    'total_price' => $lineTotal,
                ];

                if ($type === 'item') {
                    $detailData['item_id'] = $productId;
                    $item = Item::find($productId);
                    if ($item) {
                        $item->increment('stock', $qty);
                        $item->update(['price' => $price]); // Update last purchase price
                        $this->adjustStoreQuantity($request->store_id, 'item_id', $productId, $qty);
                    }
                } elseif ($type === 'ingredient') {
                    $detailData['ingredient_id'] = $productId;
                    $ingredient = Ingredient::find($productId);
                    if ($ingredient) {
                        $ingredient->increment('stock', $qty);
                        $ingredient->update(['purchase_price' => $price]);
                        $this->adjustStoreQuantity($request->store_id, 'ingredient_id', $productId, $qty);
                        Ingredient::updateFoodCostPrices($productId, $price);
                    }
                } elseif ($type === 'food') {
                    $detailData['food_id'] = $productId;
                    $food = Food::find($productId);
                    if ($food) {
                        $food->increment('stock', $qty);
                        $food->update(['cost_price' => $price]);
                        $this->adjustStoreQuantity($request->store_id, 'food_id', $productId, $qty);
                    }
                }

                PurchaseDetail::create($detailData);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Purchase record created successfully!']);
            }
            toast('Purchase record created successfully!', 'success');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Error creating purchase: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Error creating purchase: ' . $e->getMessage());
        }
    }

    public function editData($id)
    {
        $purchase = Purchase::with(['details.item.unit', 'details.ingredient.buyingUnit', 'details.food', 'details.unit', 'store', 'supplier'])->find($id);

        if (!$purchase) {
            return response()->json(['status' => false, 'message' => 'Purchase not found'], 404);
        }

        $lineItems = [];
        foreach ($purchase->details as $d) {
            $type = $d->product_type;
            $productId = 0;
            $name = '';
            $unitName = '-';
            $stock = 0;

            if ($d->item) {
                $type = 'item';
                $productId = $d->item_id;
                $name = $d->item->name;
                $unitName = $d->unit ? $d->unit->unit : ($d->item->unit ? $d->item->unit->unit : '-');
                $stock = max(0, floatval($d->item->stock ?? 0));
            } elseif ($d->ingredient) {
                $type = 'ingredient';
                $productId = $d->ingredient_id;
                $name = $d->ingredient->name;
                $unitName = $d->unit ? $d->unit->unit : ($d->ingredient->buyingUnit ? $d->ingredient->buyingUnit->unit : '-');
                $stock = max(0, floatval($d->ingredient->stock ?? 0));
            } elseif ($d->food) {
                $type = 'food';
                $productId = $d->food_id;
                $name = $d->food->name;
                $unitName = $d->unit ? $d->unit->unit : 'Serving';
                $stock = max(0, floatval($d->food->stock ?? 0));
            }

            $lineItems[] = [
                'id' => $d->id,
                'type' => $type,
                'product_id' => $productId,
                'name' => $name,
                'unit_id' => $d->unit_id,
                'unit_name' => $unitName,
                'available_stock' => $stock,
                'quantity' => floatval($d->quantity),
                'price' => floatval($d->price),
                'total_price' => floatval($d->total_price),
            ];
        }

        return response()->json([
            'status' => true,
            'purchase' => [
                'id' => $purchase->id,
                'store_id' => $purchase->store_id,
                'store_name' => $purchase->store ? ($purchase->store->name ?? $purchase->store->store) : '-',
                'supplier_id' => $purchase->supplier_id,
                'supplier_name' => $purchase->supplier ? $purchase->supplier->name : '-',
                'po_no' => $purchase->po_no,
                'total' => floatval($purchase->total),
                'purchase_date' => $purchase->purchase_date ? $purchase->purchase_date->format('Y-m-d') : '',
                'notes' => $purchase->notes,
                'items' => $lineItems,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        $request->validate([
            'store_id' => 'required|exists:cafe_stores,id',
            'supplier_id' => 'required|exists:cafe_suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:item,ingredient,food',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Revert previous stock increments
            foreach ($purchase->details as $oldDetail) {
                if ($oldDetail->item_id) {
                    $item = Item::find($oldDetail->item_id);
                    if ($item) {
                        $item->update(['stock' => max(0, floatval($item->stock) - floatval($oldDetail->quantity))]);
                    }
                    $this->adjustStoreQuantity($purchase->store_id, 'item_id', $oldDetail->item_id, -$oldDetail->quantity);
                } elseif ($oldDetail->ingredient_id) {
                    $ingredient = Ingredient::find($oldDetail->ingredient_id);
                    if ($ingredient) {
                        $ingredient->update(['stock' => max(0, floatval($ingredient->stock) - floatval($oldDetail->quantity))]);
                    }
                    $this->adjustStoreQuantity($purchase->store_id, 'ingredient_id', $oldDetail->ingredient_id, -$oldDetail->quantity);
                } elseif ($oldDetail->food_id) {
                    $food = Food::find($oldDetail->food_id);
                    if ($food) {
                        $food->update(['stock' => max(0, floatval($food->stock) - floatval($oldDetail->quantity))]);
                    }
                    $this->adjustStoreQuantity($purchase->store_id, 'food_id', $oldDetail->food_id, -$oldDetail->quantity);
                }
            }

            // Remove old details
            $purchase->details()->delete();

            // 2. Compute new totals
            $totalAmount = 0;
            foreach ($request->items as $row) {
                $qty = floatval($row['quantity'] ?? 0);
                $price = floatval($row['price'] ?? 0);
                $totalAmount += ($qty * $price);
            }

            // Update purchase header
            $purchase->update([
                'store_id' => $request->store_id,
                'supplier_id' => $request->supplier_id,
                'po_no' => $request->po_no ?? $purchase->po_no,
                'total' => $totalAmount,
                'purchase_date' => $request->purchase_date,
                'notes' => $request->notes ?? null,
                'modified_by' => Auth::id(),
                'modified_at' => now(),
            ]);

            // 3. Create new details and apply new stock increments
            foreach ($request->items as $row) {
                $type = $row['type'];
                $productId = $row['product_id'];
                $unitId = !empty($row['unit_id']) ? $row['unit_id'] : null;
                $qty = floatval($row['quantity']);
                $price = floatval($row['price']);
                $lineTotal = round($qty * $price, 2);

                $detailData = [
                    'purchase_id' => $purchase->id,
                    'item_id' => null,
                    'ingredient_id' => null,
                    'food_id' => null,
                    'unit_id' => $unitId,
                    'quantity' => $qty,
                    'price' => $price,
                    'total_price' => $lineTotal,
                ];

                if ($type === 'item') {
                    $detailData['item_id'] = $productId;
                    $item = Item::find($productId);
                    if ($item) {
                        $item->increment('stock', $qty);
                        $item->update(['price' => $price]);
                        $this->adjustStoreQuantity($request->store_id, 'item_id', $productId, $qty);
                    }
                } elseif ($type === 'ingredient') {
                    $detailData['ingredient_id'] = $productId;
                    $ingredient = Ingredient::find($productId);
                    if ($ingredient) {
                        $ingredient->increment('stock', $qty);
                        $ingredient->update(['purchase_price' => $price]);
                        $this->adjustStoreQuantity($request->store_id, 'ingredient_id', $productId, $qty);
                        Ingredient::updateFoodCostPrices($productId, $price);
                    }
                } elseif ($type === 'food') {
                    $detailData['food_id'] = $productId;
                    $food = Food::find($productId);
                    if ($food) {
                        $food->increment('stock', $qty);
                        $food->update(['cost_price' => $price]);
                        $this->adjustStoreQuantity($request->store_id, 'food_id', $productId, $qty);
                    }
                }

                PurchaseDetail::create($detailData);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Purchase record updated successfully!']);
            }
            toast('Purchase record updated successfully!', 'success');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Error updating purchase: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Error updating purchase: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Revert stock increments
            foreach ($purchase->details as $d) {
                if ($d->item_id) {
                    $item = Item::find($d->item_id);
                    if ($item) {
                        $item->update(['stock' => max(0, floatval($item->stock) - floatval($d->quantity))]);
                    }
                    $this->adjustStoreQuantity($purchase->store_id, 'item_id', $d->item_id, -$d->quantity);
                } elseif ($d->ingredient_id) {
                    $ingredient = Ingredient::find($d->ingredient_id);
                    if ($ingredient) {
                        $ingredient->update(['stock' => max(0, floatval($ingredient->stock) - floatval($d->quantity))]);
                    }
                    $this->adjustStoreQuantity($purchase->store_id, 'ingredient_id', $d->ingredient_id, -$d->quantity);
                } elseif ($d->food_id) {
                    $food = Food::find($d->food_id);
                    if ($food) {
                        $food->update(['stock' => max(0, floatval($food->stock) - floatval($d->quantity))]);
                    }
                    $this->adjustStoreQuantity($purchase->store_id, 'food_id', $d->food_id, -$d->quantity);
                }
            }

            $purchase->delete();
            DB::commit();

            toast('Purchase record deleted successfully!', 'success');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting purchase: ' . $e->getMessage());
        }
    }

    private function adjustStoreQuantity($storeId, $column, $productId, $quantityChange)
    {
        $qtyRecord = Quantity::where('store_id', $storeId)
            ->where($column, $productId)
            ->first();

        if ($qtyRecord) {
            $newQty = max(0, floatval($qtyRecord->quantity) + floatval($quantityChange));
            $qtyRecord->update(['quantity' => $newQty]);
        } else {
            Quantity::create([
                'store_id' => $storeId,
                $column => $productId,
                'quantity' => max(0, floatval($quantityChange)),
            ]);
        }
    }

    /**
     * AJAX endpoint to fetch real-time product price, stock, and unit
     */
    public function getProductInfo(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');

        if ($type === 'item') {
            $item = Item::with('unit')->find($id);
            if ($item) {
                return response()->json([
                    'status' => true,
                    'price' => (float)$item->price,
                    'stock' => max(0, (float)($item->stock ?? 0)),
                    'unit_id' => $item->unit_id,
                    'unit_name' => $item->unit ? $item->unit->unit : '-',
                ]);
            }
        } elseif ($type === 'ingredient') {
            $ing = Ingredient::with('buyingUnit')->find($id);
            if ($ing) {
                return response()->json([
                    'status' => true,
                    'price' => (float)$ing->purchase_price,
                    'stock' => max(0, (float)($ing->stock ?? 0)),
                    'unit_id' => $ing->buying_unit_id,
                    'unit_name' => $ing->buyingUnit ? $ing->buyingUnit->unit : '-',
                ]);
            }
        } elseif ($type === 'food') {
            $food = Food::find($id);
            if ($food) {
                return response()->json([
                    'status' => true,
                    'price' => (float)($food->cost_price > 0 ? $food->cost_price : $food->price),
                    'stock' => max(0, (float)($food->stock ?? 0)),
                    'unit_id' => null,
                    'unit_name' => 'Serving',
                ]);
            }
        }

        return response()->json(['status' => false, 'message' => 'Product not found.'], 404);
    }
}
