<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Ingredient;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\Quantity;
use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase return management')->only(['index', 'show']);
        $this->middleware('permission:purchase return add')->only(['store', 'getPurchases', 'getPurchaseItems', 'generateReturnNo']);
        $this->middleware('permission:purchase return delete')->only(['destroy']);
    }

    /**
     * Display listing of purchase returns or render server-side DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->getPurchaseReturnsDataAjax($request);
        }

        $stores = Store::where('status', 'A')->orWhere('status', '1')->orderBy('store', 'asc')->get();
        if ($stores->isEmpty()) {
            $stores = Store::orderBy('store', 'asc')->get();
        }

        $suppliers = Supplier::where('status', 'A')->orWhere('status', '1')->orderBy('name')->get();
        if ($suppliers->isEmpty()) {
            $suppliers = Supplier::orderBy('name')->get();
        }

        $nextReturnNo = $this->getAutoReturnNo();

        return view('admin.purchase-returns.index', compact('stores', 'suppliers', 'nextReturnNo'));
    }

    /**
     * Server-side DataTables AJAX handler
     */
    public function getPurchaseReturnsDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = $request->input('search.value');

            $recordsTotal = PurchaseReturn::count();

            $query = PurchaseReturn::with(['store', 'supplier', 'purchase', 'details']);

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('return_no', 'like', "%{$searchValue}%")
                      ->orWhere('total_amount', 'like', "%{$searchValue}%")
                      ->orWhere('return_date', 'like', "%{$searchValue}%")
                      ->orWhere('reason', 'like', "%{$searchValue}%")
                      ->orWhereHas('purchase', function ($pq) use ($searchValue) {
                          $pq->where('po_no', 'like', "%{$searchValue}%");
                      })
                      ->orWhereHas('store', function ($sq) use ($searchValue) {
                          $sq->where('store', 'like', "%{$searchValue}%");
                      })
                      ->orWhereHas('supplier', function ($supq) use ($searchValue) {
                          $supq->where('name', 'like', "%{$searchValue}%");
                      });
                });
            }

            $recordsFiltered = $query->count();

            $query->orderBy('id', 'desc');

            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $returns = $query->get();

            $user = auth()->user();
            $canDelete = $user ? $user->can('purchase return delete') : true;

            $data = [];
            foreach ($returns as $key => $r) {
                $rowIndex = $start + $key + 1;
                $returnNo = e($r->return_no ?? 'PR-' . str_pad($r->id, 4, '0', STR_PAD_LEFT));
                $poNumber = e($r->purchase ? ($r->purchase->po_no ?? 'PO-' . str_pad($r->purchase->id, 4, '0', STR_PAD_LEFT)) : ($r->po_no ?? '-'));
                $storeName = e($r->store ? ($r->store->store ?? $r->store->name) : '-');
                $supplierName = e($r->supplier ? $r->supplier->name : '-');
                $dateFormatted = $r->return_date ? $r->return_date->format('d-M-Y') : '-';
                $detailsCount = $r->details ? $r->details->count() : 0;
                $totalFormatted = 'Rs. ' . number_format(floatval($r->total_amount), 2);

                $actions = '<div class="d-flex align-items-center justify-content-center gap-1">';
                
                // View Details Button
                $actions .= '<button type="button" class="btn btn-sm btn-outline-primary view-return-btn" data-id="' . $r->id . '" title="View Return Details">
                                <i class="bx bx-show-alt"></i>
                             </button>';

                // Delete Button
                if ($canDelete) {
                    $actions .= '<button type="button" class="btn btn-sm btn-outline-danger delete-return-btn" data-id="' . $r->id . '" data-return-no="' . $returnNo . '" title="Delete Return & Restore Stock">
                                    <i class="bx bx-trash"></i>
                                 </button>';
                }

                $actions .= '</div>';

                $data[] = [
                    'index' => $rowIndex,
                    'return_no' => '<span class="badge bg-label-warning fw-semibold px-2 py-1"><i class="bx bx-rotate-left me-1"></i>' . $returnNo . '</span>',
                    'po_no' => '<span class="badge bg-label-info fw-semibold px-2 py-1"><i class="bx bx-receipt me-1"></i>' . $poNumber . '</span>',
                    'store' => '<span class="fw-medium text-dark">' . $storeName . '</span>',
                    'supplier' => '<span class="fw-medium text-dark">' . $supplierName . '</span>',
                    'return_date' => '<span class="text-muted"><i class="bx bx-calendar me-1"></i>' . $dateFormatted . '</span>',
                    'items_count' => '<span class="badge bg-label-secondary rounded-pill">' . $detailsCount . ' items</span>',
                    'total_amount' => '<span class="fw-bold text-danger">' . $totalFormatted . '</span>',
                    'options' => $actions,
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX endpoint to fetch purchases filtered by store and/or supplier
     */
    public function getPurchases(Request $request)
    {
        $storeId = $request->input('store_id');
        $supplierId = $request->input('supplier_id');

        $query = Purchase::with(['store', 'supplier']);

        if (!empty($storeId)) {
            $query->where('store_id', $storeId);
        }

        if (!empty($supplierId)) {
            $query->where('supplier_id', $supplierId);
        }

        $purchases = $query->orderBy('id', 'desc')->get();

        $data = [];
        foreach ($purchases as $p) {
            $poNo = $p->po_no ?? 'PO-' . str_pad($p->id, 4, '0', STR_PAD_LEFT);
            $storeName = $p->store ? ($p->store->store ?? $p->store->name) : 'Store #' . $p->store_id;
            $supplierName = $p->supplier ? $p->supplier->name : 'Supplier #' . $p->supplier_id;
            $date = $p->purchase_date ? $p->purchase_date->format('Y-m-d') : '';
            $total = number_format(floatval($p->total), 2);

            $data[] = [
                'id' => $p->id,
                'po_no' => $poNo,
                'store_id' => $p->store_id,
                'store_name' => $storeName,
                'supplier_id' => $p->supplier_id,
                'supplier_name' => $supplierName,
                'purchase_date' => $date,
                'total' => floatval($p->total),
                'label' => $poNo,
                'full_label' => "{$poNo} (Rs. {$total} - {$date})",
            ];
        }

        return response()->json([
            'status' => true,
            'purchases' => $data,
        ]);
    }

    /**
     * AJAX endpoint to fetch details of a purchase for returning items
     */
    public function getPurchaseItems($purchaseId)
    {
        $purchase = Purchase::with([
            'details.item.unit',
            'details.ingredient.buyingUnit',
            'details.food',
            'details.unit',
            'store',
            'supplier'
        ])->find($purchaseId);

        if (!$purchase) {
            return response()->json([
                'status' => false,
                'message' => 'Purchase record not found',
            ], 404);
        }

        $items = [];
        foreach ($purchase->details as $d) {
            $type = $d->product_type;
            $name = '';
            $unitName = '-';

            if ($d->item) {
                $type = 'item';
                $name = $d->item->name;
                $unitName = $d->unit ? $d->unit->unit : ($d->item->unit ? $d->item->unit->unit : 'Pcs');
            } elseif ($d->ingredient) {
                $type = 'ingredient';
                $name = $d->ingredient->name;
                $unitName = $d->unit ? $d->unit->unit : ($d->ingredient->buyingUnit ? $d->ingredient->buyingUnit->unit : 'Kg');
            } elseif ($d->food) {
                $type = 'food';
                $name = $d->food->name;
                $unitName = $d->unit ? $d->unit->unit : 'Serving';
            }

            // Calculate already returned quantity for this purchase detail line
            $alreadyReturned = PurchaseReturnDetail::where('purchase_detail_id', $d->id)->sum('quantity');
            $purchasedQty = floatval($d->quantity);
            $returnedQty = floatval($alreadyReturned);
            $availableToReturn = max(0, $purchasedQty - $returnedQty);
            $unitPrice = floatval($d->price);

            $items[] = [
                'purchase_detail_id' => $d->id,
                'product_type' => $type,
                'name' => $name,
                'unit_id' => $d->unit_id,
                'unit_name' => $unitName,
                'purchased_qty' => $purchasedQty,
                'already_returned_qty' => $returnedQty,
                'available_to_return' => $availableToReturn,
                'unit_price' => $unitPrice,
                'subtotal' => 0,
            ];
        }

        return response()->json([
            'status' => true,
            'purchase' => [
                'id' => $purchase->id,
                'po_no' => $purchase->po_no,
                'store_id' => $purchase->store_id,
                'store_name' => $purchase->store ? ($purchase->store->store ?? $purchase->store->name) : '-',
                'supplier_id' => $purchase->supplier_id,
                'supplier_name' => $purchase->supplier ? $purchase->supplier->name : '-',
                'purchase_date' => $purchase->purchase_date ? $purchase->purchase_date->format('Y-m-d') : '',
                'total' => floatval($purchase->total),
                'items' => $items,
            ]
        ]);
    }

    /**
     * Store a new purchase return record & deduct inventory stock
     */
    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:cafe_stores,id',
            'supplier_id' => 'required|exists:cafe_suppliers,id',
            'purchase_id' => 'required|exists:cafe_purchases,id',
            'return_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.purchase_detail_id' => 'required|exists:cafe_purchase_details,id',
            'items.*.return_qty' => 'required|numeric|min:0',
        ]);

        // Filter items with return_qty > 0
        $returnItems = [];
        foreach ($request->items as $row) {
            $qty = floatval($row['return_qty'] ?? 0);
            if ($qty > 0) {
                $returnItems[] = $row;
            }
        }

        if (empty($returnItems)) {
            return response()->json([
                'status' => false,
                'message' => 'Please enter a return quantity greater than 0 for at least one item.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($request->purchase_id);
            $returnNo = $this->getAutoReturnNo();

            // Validate that return quantity does not exceed available purchased quantity
            $totalReturnAmount = 0;
            $validatedDetails = [];

            foreach ($returnItems as $row) {
                $detail = PurchaseDetail::with(['item', 'ingredient', 'food', 'unit'])->findOrFail($row['purchase_detail_id']);
                $qtyToReturn = floatval($row['return_qty']);

                $alreadyReturned = PurchaseReturnDetail::where('purchase_detail_id', $detail->id)->sum('quantity');
                $purchasedQty = floatval($detail->quantity);
                $availableToReturn = max(0, $purchasedQty - floatval($alreadyReturned));

                if ($qtyToReturn > $availableToReturn) {
                    $prodName = $detail->product_name;
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Return quantity for '{$prodName}' ({$qtyToReturn}) exceeds available returnable quantity ({$availableToReturn}).",
                    ], 422);
                }

                $price = floatval($detail->price);
                $subtotal = round($qtyToReturn * $price, 2);
                $totalReturnAmount += $subtotal;

                $validatedDetails[] = [
                    'detail' => $detail,
                    'quantity' => $qtyToReturn,
                    'price' => $price,
                    'total_price' => $subtotal,
                ];
            }

            // Create Purchase Return Header
            $purchaseReturn = PurchaseReturn::create([
                'return_no' => $returnNo,
                'purchase_id' => $purchase->id,
                'store_id' => $request->store_id,
                'supplier_id' => $request->supplier_id,
                'return_date' => $request->return_date,
                'total_amount' => $totalReturnAmount,
                'reason' => $request->reason ?? $request->notes ?? null,
                'created_by' => Auth::id(),
            ]);

            // Create Return Details and DEDUCT Inventory Stock
            foreach ($validatedDetails as $item) {
                $detail = $item['detail'];
                $qty = $item['quantity'];
                $price = $item['price'];
                $subtotal = $item['total_price'];

                PurchaseReturnDetail::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_detail_id' => $detail->id,
                    'item_id' => $detail->item_id,
                    'ingredient_id' => $detail->ingredient_id,
                    'food_id' => $detail->food_id,
                    'unit_id' => $detail->unit_id,
                    'quantity' => $qty,
                    'price' => $price,
                    'total_price' => $subtotal,
                ]);

                // Inventory Stock Deduction
                if ($detail->item_id) {
                    $itemModel = Item::find($detail->item_id);
                    if ($itemModel) {
                        $itemModel->update(['stock' => max(0, floatval($itemModel->stock) - $qty)]);
                    }
                    $this->adjustStoreQuantity($request->store_id, 'item_id', $detail->item_id, -$qty);
                } elseif ($detail->ingredient_id) {
                    $ingredientModel = Ingredient::find($detail->ingredient_id);
                    if ($ingredientModel) {
                        $ingredientModel->update(['stock' => max(0, floatval($ingredientModel->stock) - $qty)]);
                    }
                    $this->adjustStoreQuantity($request->store_id, 'ingredient_id', $detail->ingredient_id, -$qty);
                } elseif ($detail->food_id) {
                    $foodModel = Food::find($detail->food_id);
                    if ($foodModel) {
                        $foodModel->update(['stock' => max(0, floatval($foodModel->stock) - $qty)]);
                    }
                    $this->adjustStoreQuantity($request->store_id, 'food_id', $detail->food_id, -$qty);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Purchase Return record saved and inventory stock deducted successfully!',
                'return_no' => $returnNo,
                'total_amount' => $totalReturnAmount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error recording purchase return: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View details of a specific purchase return
     */
    public function show($id)
    {
        $return = PurchaseReturn::with([
            'purchase',
            'store',
            'supplier',
            'creator',
            'details.item.unit',
            'details.ingredient.buyingUnit',
            'details.food',
            'details.unit',
        ])->find($id);

        if (!$return) {
            return response()->json([
                'status' => false,
                'message' => 'Purchase return record not found',
            ], 404);
        }

        $items = [];
        foreach ($return->details as $d) {
            $name = $d->product_name;
            $unitName = '-';
            if ($d->item) {
                $unitName = $d->unit ? $d->unit->unit : ($d->item->unit ? $d->item->unit->unit : 'Pcs');
            } elseif ($d->ingredient) {
                $unitName = $d->unit ? $d->unit->unit : ($d->ingredient->buyingUnit ? $d->ingredient->buyingUnit->unit : 'Kg');
            } elseif ($d->food) {
                $unitName = $d->unit ? $d->unit->unit : 'Serving';
            }

            $items[] = [
                'name' => $name,
                'type' => $d->product_type,
                'unit' => $unitName,
                'quantity' => floatval($d->quantity),
                'price' => floatval($d->price),
                'total_price' => floatval($d->total_price),
            ];
        }

        return response()->json([
            'status' => true,
            'return' => [
                'id' => $return->id,
                'return_no' => $return->return_no,
                'po_no' => $return->purchase ? $return->purchase->po_no : '-',
                'store_name' => $return->store ? ($return->store->store ?? $return->store->name) : '-',
                'supplier_name' => $return->supplier ? $return->supplier->name : '-',
                'return_date' => $return->return_date ? $return->return_date->format('Y-m-d') : '-',
                'total_amount' => floatval($return->total_amount),
                'reason' => $return->reason ?? 'N/A',
                'created_by' => $return->creator ? $return->creator->name : 'System',
                'items' => $items,
            ]
        ]);
    }

    /**
     * Delete purchase return and restore stock
     */
    public function destroy($id)
    {
        $return = PurchaseReturn::with('details')->find($id);

        if (!$return) {
            return response()->json([
                'status' => false,
                'message' => 'Purchase return record not found',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Restore inventory stock for each returned item
            foreach ($return->details as $d) {
                if ($d->item_id) {
                    $item = Item::find($d->item_id);
                    if ($item) {
                        $item->increment('stock', $d->quantity);
                    }
                    $this->adjustStoreQuantity($return->store_id, 'item_id', $d->item_id, $d->quantity);
                } elseif ($d->ingredient_id) {
                    $ingredient = Ingredient::find($d->ingredient_id);
                    if ($ingredient) {
                        $ingredient->increment('stock', $d->quantity);
                    }
                    $this->adjustStoreQuantity($return->store_id, 'ingredient_id', $d->ingredient_id, $d->quantity);
                } elseif ($d->food_id) {
                    $food = Food::find($d->food_id);
                    if ($food) {
                        $food->increment('stock', $d->quantity);
                    }
                    $this->adjustStoreQuantity($return->store_id, 'food_id', $d->food_id, $d->quantity);
                }
            }

            $return->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Purchase return deleted and inventory stock restored successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error deleting purchase return: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Adjust store level quantity in cafe_quantities
     */
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
     * Generate Next Return Number (PR-YYYYMMDD-0001)
     */
    private function getAutoReturnNo()
    {
        $todayPrefix = 'PR-' . date('Ymd') . '-';
        $lastReturn = PurchaseReturn::where('return_no', 'like', "{$todayPrefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReturn && preg_match('/-(\d+)$/', $lastReturn->return_no, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }

        return $todayPrefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
