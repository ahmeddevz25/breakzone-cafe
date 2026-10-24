<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComboProduct;
use App\Models\ComboProductDetail;
use App\Models\Food;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ComboProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:combo product management')->only('index');
        $this->middleware('permission:combo product add')->only(['store', 'generateCode']);
        $this->middleware('permission:combo product edit')->only(['update', 'getComboDetails', 'toggleStatus']);
        $this->middleware('permission:combo product delete')->only('destroy');
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
                return $this->getComboProductsDataAjax($request);
            }

            $items = Item::with('unit')->where('status', 'A')->orderBy('name', 'asc')->get();
            $foods = Food::where('status', 'A')->orderBy('name', 'asc')->get();

            return view('admin.combo-products.index', compact('items', 'foods'));
        } catch (\Exception $e) {
            Log::error('Combo Product Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching combo products.');
        }
    }

    public function getComboProductsDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = $request->input('search.value');
            $statusFilter = $request->input('status_filter');

            $recordsTotal = ComboProduct::count();

            $query = ComboProduct::with(['details.item.unit', 'details.food']);

            if (!empty($statusFilter)) {
                $query->where('status', $statusFilter);
            }

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('price', 'like', "%{$searchValue}%");

                    if (stripos('Active', $searchValue) !== false) {
                        $q->orWhere('status', 'A');
                    } elseif (stripos('Inactive', $searchValue) !== false) {
                        $q->orWhere('status', 'I');
                    }
                });
            }

            $recordsFiltered = $query->count();

            $query->orderBy('id', 'desc');

            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $combos = $query->get();

            $user = auth()->user();
            $canEdit = $user ? $user->can('combo product edit') : true;
            $canDelete = $user ? $user->can('combo product delete') : true;

            $data = [];
            foreach ($combos as $key => $combo) {
                $rowIndex = $start + $key + 1;

                // Price
                $priceFormatted = number_format((float)$combo->price, 2);

                // Status badge matching other modules
                $statusHtml = ($combo->status === 'A')
                    ? '<span class="badge bg-success">ACTIVE</span>'
                    : '<span class="badge bg-danger">INACTIVE</span>';

                // Options buttons matching other modules
                $actionsHtml = '<div class="table-actions">';

                // 1. View button
                $actionsHtml .= '<button type="button" class="action-btn action-btn-view" title="View Details" onclick="viewComboDetails(' . $combo->id . ')">
                                    <i class="bx bx-show"></i>
                                 </button>';

                // 2. Edit button
                if ($canEdit) {
                    $actionsHtml .= '<button type="button" class="action-btn action-btn-edit" title="Edit Combo Product" onclick="editComboProduct(' . $combo->id . ')">
                                        <i class="bx bx-edit"></i>
                                     </button>';
                }

                // 3. Delete button
                if ($canDelete) {
                    $deleteUrl = route('combo-products.delete', $combo->id);
                    $actionsHtml .= '<button type="button" class="action-btn action-btn-delete" title="Delete Combo Product"
                                        onclick="confirmation(event)"
                                        data-url="' . $deleteUrl . '"
                                        data-title="Delete Combo Product?"
                                        data-text="Are you sure you want to delete &quot;' . e($combo->name) . '&quot;?">
                                        <i class="bx bx-trash"></i>
                                     </button>';
                }

                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => $rowIndex,
                    'name' => e($combo->name),
                    'price' => $priceFormatted,
                    'status' => $statusHtml,
                    'options' => $actionsHtml,
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Combo Products AJAX Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateUniqueCode()
    {
        $last = ComboProduct::latest('id')->first();
        $nextId = $last ? ($last->id + 1) : 1;
        $code = 'CMB-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        while (ComboProduct::where('code', $code)->exists()) {
            $nextId++;
            $code = 'CMB-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function getComboDetails($id)
    {
        try {
            $combo = ComboProduct::with(['details.item.unit', 'details.food'])->find($id);

            if (!$combo) {
                return response()->json(['status' => false, 'message' => 'Combo Product not found.'], 404);
            }

            $details = $combo->details->map(function ($detail) {
                $isItem = (bool)$detail->item_id;
                $prod = $isItem ? $detail->item : $detail->food;
                $prodName = $prod ? $prod->name : 'N/A';

                $unitName = '-';
                if ($isItem && $detail->item && $detail->item->unit) {
                    $unitName = $detail->item->unit->unit ?? $detail->item->unit->name ?? '-';
                } elseif (!$isItem) {
                    $unitName = 'Serving';
                }

                $salePrice = (float)$detail->price;
                if ($salePrice <= 0 && $prod) {
                    $salePrice = (float)($isItem ? $prod->sale_price : $prod->price);
                }

                $qty = (float)$detail->quantity;
                $discount = (float)$detail->discount;
                $lineTotal = max(0, round(($qty * $salePrice) - $discount, 2));

                return [
                    'id' => $detail->id,
                    'type' => $isItem ? 'item' : 'food',
                    'product_id' => $isItem ? $detail->item_id : $detail->food_id,
                    'product_name' => $prodName,
                    'unit' => $unitName,
                    'quantity' => $qty,
                    'sale_price' => $salePrice,
                    'discount' => $discount,
                    'total_price' => $lineTotal,
                ];
            });

            return response()->json([
                'status' => true,
                'combo' => [
                    'id' => $combo->id,
                    'name' => $combo->name,
                    'code' => $combo->code,
                    'price' => (float)$combo->price,
                    'status' => $combo->status,
                    'picture_url' => $combo->picture_url,
                    'details' => $details,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Get Combo Details Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to load details: ' . $e->getMessage()], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $combo = ComboProduct::findOrFail($id);
            $combo->status = ($combo->status === 'A') ? 'I' : 'A';
            $combo->save();

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully.',
                'new_status' => $combo->status,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'components' => 'required|array|min:1',
            'components.*.type' => 'required|in:item,food',
            'components.*.product_id' => 'required|numeric',
            'components.*.quantity' => 'nullable|numeric|min:0',
            'components.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $status = strtoupper(trim($request->status ?? 'A'));
            $statusChar = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            $picturePath = null;
            if ($request->hasFile('picture')) {
                $picturePath = $request->file('picture')->store('combo_products', 'public');
            }

            $code = $this->generateUniqueCode();

            $combo = ComboProduct::create([
                'store_id' => null,
                'name' => $request->name,
                'code' => $code,
                'price' => (float)($request->price ?? 0),
                'picture' => $picturePath,
                'status' => $statusChar,
                'position' => 0,
                'created_by' => auth()->id() ?? 1,
                'modified_by' => auth()->id() ?? 1,
                'modified_at' => now(),
            ]);

            foreach ($request->components as $row) {
                $type = $row['type'] ?? 'item';
                $prodId = $row['product_id'] ?? null;
                $qty = isset($row['quantity']) ? (float)$row['quantity'] : 0;
                $discount = isset($row['discount']) ? (float)$row['discount'] : 0;

                // Lookup original sale price
                $salePrice = 0;
                if ($type === 'item' && $prodId) {
                    $item = Item::find($prodId);
                    $salePrice = $item ? (float)$item->sale_price : 0;
                } elseif ($type === 'food' && $prodId) {
                    $food = Food::find($prodId);
                    $salePrice = $food ? (float)$food->price : 0;
                }

                $lineTotal = max(0, round(($qty * $salePrice) - $discount, 2));

                if ($prodId) {
                    $combo->details()->create([
                        'item_id' => ($type === 'item') ? $prodId : null,
                        'food_id' => ($type === 'food') ? $prodId : null,
                        'quantity' => $qty,
                        'price' => $salePrice,
                        'discount' => $discount,
                        'total_price' => $lineTotal,
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Combo Product saved successfully.']);
            }
            return redirect()->route('combo-products.index')->with('success', 'Combo Product saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Combo Product Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to save combo: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to save combo: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $combo = ComboProduct::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:250',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'components' => 'required|array|min:1',
            'components.*.type' => 'required|in:item,food',
            'components.*.product_id' => 'required|numeric',
            'components.*.quantity' => 'nullable|numeric|min:0',
            'components.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $status = strtoupper(trim($request->status ?? 'A'));
            $statusChar = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            $picturePath = $combo->picture;
            if ($request->hasFile('picture')) {
                if (!empty($combo->picture) && Storage::disk('public')->exists($combo->picture)) {
                    Storage::disk('public')->delete($combo->picture);
                }
                $picturePath = $request->file('picture')->store('combo_products', 'public');
            }

            $combo->update([
                'name' => $request->name,
                'price' => (float)($request->price ?? 0),
                'picture' => $picturePath,
                'status' => $statusChar,
                'modified_by' => auth()->id() ?? 1,
                'modified_at' => now(),
            ]);

            // Re-sync components
            $combo->details()->delete();

            foreach ($request->components as $row) {
                $type = $row['type'] ?? 'item';
                $prodId = $row['product_id'] ?? null;
                $qty = isset($row['quantity']) ? (float)$row['quantity'] : 0;
                $discount = isset($row['discount']) ? (float)$row['discount'] : 0;

                // Lookup original sale price
                $salePrice = 0;
                if ($type === 'item' && $prodId) {
                    $item = Item::find($prodId);
                    $salePrice = $item ? (float)$item->sale_price : 0;
                } elseif ($type === 'food' && $prodId) {
                    $food = Food::find($prodId);
                    $salePrice = $food ? (float)$food->price : 0;
                }

                $lineTotal = max(0, round(($qty * $salePrice) - $discount, 2));

                if ($prodId) {
                    $combo->details()->create([
                        'item_id' => ($type === 'item') ? $prodId : null,
                        'food_id' => ($type === 'food') ? $prodId : null,
                        'quantity' => $qty,
                        'price' => $salePrice,
                        'discount' => $discount,
                        'total_price' => $lineTotal,
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Combo Product updated successfully.']);
            }
            return redirect()->route('combo-products.index')->with('success', 'Combo Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Combo Product Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update combo: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to update combo: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $combo = ComboProduct::findOrFail($id);

            if (!empty($combo->picture) && Storage::disk('public')->exists($combo->picture)) {
                Storage::disk('public')->delete($combo->picture);
            }

            $combo->details()->delete();
            $combo->delete();

            DB::commit();

            return redirect()->route('combo-products.index')->with('success', 'Combo Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Combo Product Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete combo: ' . $e->getMessage());
        }
    }
}
