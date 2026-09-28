<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Item;
use App\Models\Store;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:item management')->only('index');
        $this->middleware('permission:item add')->only('store');
        $this->middleware('permission:item edit')->only('update');
        $this->middleware('permission:item delete')->only('destroy');
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
                return $this->getItemsDataAjax($request);
            }

            $stores = Store::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            $categories = Category::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            $units = Unit::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            $brands = Brand::orderBy('position', 'asc')->orderBy('id', 'asc')->get();

            return view('admin.items.index', compact('stores', 'categories', 'units', 'brands'));
        } catch (\Exception $e) {
            Log::error('Item Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching items.');
        }
    }

    public function getItemsDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = $request->input('search.value');
            $statusFilter = $request->input('status_filter');

            $recordsTotal = Item::count();

            $query = Item::with(['store', 'category', 'unit', 'brand']);

            if (!empty($statusFilter)) {
                $query->where('status', $statusFilter);
            }

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('code', 'like', "%{$searchValue}%")
                      ->orWhereHas('category', function ($cq) use ($searchValue) {
                          $cq->where('category', 'like', "%{$searchValue}%");
                      })
                      ->orWhereHas('store', function ($sq) use ($searchValue) {
                          $sq->where('store', 'like', "%{$searchValue}%");
                      });

                    if (stripos('Active', $searchValue) !== false) {
                        $q->orWhere('status', 'A');
                    } elseif (stripos('Inactive', $searchValue) !== false) {
                        $q->orWhere('status', 'I');
                    }
                });
            }

            $recordsFiltered = $query->count();

            $query->orderBy('position', 'asc')->orderBy('id', 'desc');

            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $items = $query->get();

            $user = auth()->user();
            $canEdit = $user ? $user->can('item edit') : true;
            $canDelete = $user ? $user->can('item delete') : true;

            $data = [];
            foreach ($items as $key => $item) {
                $rowIndex = $start + $key + 1;
                $itemName = e($item->name ?? '');
                $itemCode = e($item->code ?? '');
                $categoryPath = $item->category ? $item->category->full_path : '-';
                $storeName = $item->store ? ($item->store->store ?? $item->store->name ?? '-') : '-';
                $unitName = $item->unit ? ($item->unit->unit ?? $item->unit->name ?? '-') : '-';
                $brandName = $item->brand ? ($item->brand->name ?? '-') : '-';
                $pictureUrl = $item->picture_url;

                // Item Name with picture or icon
                $itemHtml = '<div class="d-flex align-items-center gap-2">';
                if ($pictureUrl) {
                    $itemHtml .= '<img src="' . e($pictureUrl) . '" alt="' . $itemName . '" class="rounded" style="width: 38px; height: 38px; object-fit: cover; border: 1px solid #e2e8f0;">';
                } else {
                    $itemHtml .= '<div class="rounded d-flex align-items-center justify-content-center bg-light text-secondary" style="width: 38px; height: 38px; border: 1px solid #e2e8f0;"><i class="bx bx-coffee fs-5"></i></div>';
                }
                $itemHtml .= '<div class="d-flex flex-column"><span class="fw-bold text-dark">' . $itemName . '</span>';
                if ($storeName !== '-') {
                    $itemHtml .= '<small class="text-muted"><i class="bx bx-store-alt me-1"></i>' . e($storeName) . '</small>';
                }
                $itemHtml .= '</div></div>';

                // Status
                $statusVal = strtoupper(trim($item->status ?? ''));
                if ($statusVal === 'A' || $statusVal === 'ACTIVE' || $statusVal === '1') {
                    $statusHtml = '<span class="badge bg-success">Active</span>';
                } else {
                    $statusHtml = '<span class="badge bg-danger">Inactive</span>';
                }

                // Options / Actions
                $actionsHtml = '<div class="table-actions justify-content-center">';
                $actionsHtml .= '<button type="button" title="View Details" class="action-btn action-btn-view view-item-btn" '
                    . 'data-name="' . $itemName . '" '
                    . 'data-store="' . e($storeName) . '" '
                    . 'data-category="' . e($categoryPath) . '" '
                    . 'data-code="' . $itemCode . '" '
                    . 'data-unit="' . e($unitName) . '" '
                    . 'data-brand="' . e($brandName) . '" '
                    . 'data-price="' . number_format($item->price, 2) . '" '
                    . 'data-sale_price="' . number_format($item->sale_price, 2) . '" '
                    . 'data-stock="' . number_format($item->stock ?? 0, 2) . '" '
                    . 'data-details="' . e($item->details ?? '-') . '" '
                    . 'data-picture="' . e($pictureUrl ?? '') . '" '
                    . 'data-status="' . e($item->status) . '" '
                    . 'data-bs-toggle="modal" data-bs-target="#viewItemModal">'
                    . '<i class="bx bx-show"></i>'
                    . '</button>';

                if ($canEdit) {
                    $actionsHtml .= '<button type="button" title="Edit" class="action-btn action-btn-edit edit-item-btn" '
                        . 'data-id="' . $item->id . '" '
                        . 'data-name="' . $itemName . '" '
                        . 'data-store_id="' . $item->store_id . '" '
                        . 'data-category_id="' . $item->category_id . '" '
                        . 'data-code="' . $itemCode . '" '
                        . 'data-unit_id="' . $item->unit_id . '" '
                        . 'data-brand_id="' . $item->brand_id . '" '
                        . 'data-price="' . $item->price . '" '
                        . 'data-sale_price="' . $item->sale_price . '" '
                        . 'data-stock="' . $item->stock . '" '
                        . 'data-details="' . e($item->details ?? '') . '" '
                        . 'data-picture="' . e($pictureUrl ?? '') . '" '
                        . 'data-status="' . e($item->status) . '" '
                        . 'data-position="' . e($item->position ?? 0) . '" '
                        . 'data-bs-toggle="modal" data-bs-target="#itemModal">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }

                if ($canDelete) {
                    $actionsHtml .= '<button type="button" title="Delete" class="action-btn action-btn-delete delete-item-ajax-btn" '
                        . 'data-url="' . route('items.delete', $item->id) . '" '
                        . 'data-name="' . $itemName . '">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => $rowIndex,
                    'item' => $itemHtml,
                    'code' => '<span class="text-dark font-monospace fw-medium">' . $itemCode . '</span>',
                    'category' => '<span class="text-dark fw-medium">' . $categoryPath . '</span>',
                    'stock' => '<span class="text-dark fw-medium">' . number_format($item->stock ?? 0, 2) . '</span>',
                    'purchase_price' => '<span class="text-dark fw-medium">' . number_format($item->price, 2) . '</span>',
                    'sale_price' => '<span class="text-dark fw-bold">' . number_format($item->sale_price, 2) . '</span>',
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
            Log::error('Item DataTables Ajax Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'store_id' => 'required|exists:cafe_stores,id',
            'category_id' => 'required|exists:cafe_categories,id',
            'code' => 'required|string|max:20|unique:cafe_items,code',
            'unit_id' => 'required|exists:cafe_units,id',
            'brand_id' => 'nullable|exists:cafe_brands,id',
            'price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
            'position' => 'nullable|integer',
        ]);

        try {
            $data = $request->except(['picture', 'stock']);
            $data['price'] = $request->input('price') ?? $request->input('purchase_price') ?? 0;
            $data['sale_price'] = $request->input('sale_price') ?? 0;
            $data['stock'] = 0;
            $data['position'] = $request->input('position') ?? 0;
            $data['details'] = $request->input('details') ?? '';

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            if ($request->hasFile('picture')) {
                $data['picture'] = $request->file('picture')->store('items', 'public');
            } else {
                $data['picture'] = '';
            }

            $data['created_by'] = auth()->id() ?? 1;
            $data['modified_by'] = auth()->id() ?? 1;
            $data['modified_at'] = now();

            Item::create($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Item created successfully.']);
            }
            return redirect()->route('items.index')->with('success', 'Item created successfully.');
        } catch (\Exception $e) {
            Log::error('Item Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create item. ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to create item. ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'store_id' => 'required|exists:cafe_stores,id',
            'category_id' => 'required|exists:cafe_categories,id',
            'code' => 'required|string|max:20|unique:cafe_items,code,' . $id,
            'unit_id' => 'required|exists:cafe_units,id',
            'brand_id' => 'nullable|exists:cafe_brands,id',
            'price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
            'position' => 'nullable|integer',
        ]);

        try {
            $item = Item::findOrFail($id);
            $data = $request->except(['picture', 'stock']);

            $data['price'] = $request->input('price') ?? $request->input('purchase_price') ?? 0;
            $data['sale_price'] = $request->input('sale_price') ?? 0;
            $data['position'] = $request->input('position') ?? 0;
            $data['details'] = $request->input('details') ?? '';

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            if ($request->hasFile('picture')) {
                // Delete old picture if exists
                if (!empty($item->picture) && Storage::disk('public')->exists($item->picture)) {
                    Storage::disk('public')->delete($item->picture);
                }
                $data['picture'] = $request->file('picture')->store('items', 'public');
            }

            $data['modified_by'] = auth()->id() ?? 1;
            $data['modified_at'] = now();

            $item->update($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Item updated successfully.']);
            }
            return redirect()->route('items.index')->with('success', 'Item updated successfully.');
        } catch (\Exception $e) {
            Log::error('Item Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update item. ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to update item. ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $item = Item::findOrFail($id);

            // Delete associated picture
            if (!empty($item->picture) && Storage::disk('public')->exists($item->picture)) {
                Storage::disk('public')->delete($item->picture);
            }

            $item->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Item deleted successfully.']);
            }

            return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Item Delete Error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to delete item: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete item. Please try again.');
        }
    }
}
