<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Store;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IngredientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ingredient management')->only('index');
        $this->middleware('permission:ingredient add')->only('store');
        $this->middleware('permission:ingredient edit')->only('update');
        $this->middleware('permission:ingredient delete')->only('destroy');
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
                return $this->getIngredientsDataAjax($request);
            }

            $stores = Store::where('status', 'A')->orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            if ($stores->isEmpty()) {
                $stores = Store::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            }

            $units = Unit::where('status', 'A')->orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            if ($units->isEmpty()) {
                $units = Unit::orderBy('position', 'asc')->orderBy('id', 'asc')->get();
            }

            return view('admin.ingredients.index', compact('stores', 'units'));
        } catch (\Exception $e) {
            Log::error('Ingredient Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching ingredients.');
        }
    }

    public function getIngredientsDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = $request->input('search.value');
            $statusFilter = $request->input('status_filter');

            $recordsTotal = Ingredient::count();

            $query = Ingredient::with(['store', 'buyingUnit', 'usageUnit']);

            if (!empty($statusFilter)) {
                $query->where('status', $statusFilter);
            }

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhereHas('store', function ($sq) use ($searchValue) {
                          $sq->where('store', 'like', "%{$searchValue}%");
                      })
                      ->orWhereHas('buyingUnit', function ($buq) use ($searchValue) {
                          $buq->where('unit', 'like', "%{$searchValue}%");
                      })
                      ->orWhereHas('usageUnit', function ($uuq) use ($searchValue) {
                          $uuq->where('unit', 'like', "%{$searchValue}%");
                      });

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

            $ingredients = $query->get();

            $user = auth()->user();
            $canEdit = $user ? $user->can('ingredient edit') : true;
            $canDelete = $user ? $user->can('ingredient delete') : true;

            $data = [];
            foreach ($ingredients as $key => $ingredient) {
                $rowIndex = $start + $key + 1;
                $ingName = e($ingredient->name ?? '');
                $storeName = $ingredient->store ? ($ingredient->store->store ?? $ingredient->store->name ?? '-') : '-';
                $buyingUnitName = $ingredient->buyingUnit ? ($ingredient->buyingUnit->unit ?? $ingredient->buyingUnit->name ?? '-') : '-';
                $usageUnitName = $ingredient->usageUnit ? ($ingredient->usageUnit->unit ?? $ingredient->usageUnit->name ?? '-') : '-';
                $pictureUrl = $ingredient->picture_url;

                // Name with image or icon
                $ingHtml = '<div class="d-flex align-items-center gap-2">';
                if ($pictureUrl) {
                    $ingHtml .= '<img src="' . e($pictureUrl) . '" alt="' . $ingName . '" class="rounded" style="width: 38px; height: 38px; object-fit: cover; border: 1px solid #e2e8f0;">';
                } else {
                    $ingHtml .= '<div class="rounded d-flex align-items-center justify-content-center bg-light text-secondary" style="width: 38px; height: 38px; border: 1px solid #e2e8f0;"><i class="bx bx-dish fs-5"></i></div>';
                }
                $ingHtml .= '<div class="d-flex flex-column"><span class="fw-bold text-dark">' . $ingName . '</span>';
                if ($storeName !== '-') {
                    $ingHtml .= '<small class="text-muted"><i class="bx bx-store-alt me-1"></i>' . e($storeName) . '</small>';
                }
                $ingHtml .= '</div></div>';

                // Status
                $statusVal = strtoupper(trim($ingredient->status ?? ''));
                if ($statusVal === 'A' || $statusVal === 'ACTIVE' || $statusVal === '1') {
                    $statusHtml = '<span class="badge bg-success">Active</span>';
                } else {
                    $statusHtml = '<span class="badge bg-danger">Inactive</span>';
                }

                // Actions
                $actionsHtml = '<div class="table-actions justify-content-center">';
                $actionsHtml .= '<button type="button" title="View Details" class="action-btn action-btn-view view-ingredient-btn" '
                    . 'data-name="' . $ingName . '" '
                    . 'data-store="' . e($storeName) . '" '
                    . 'data-buying_unit="' . e($buyingUnitName) . '" '
                    . 'data-usage_unit="' . e($usageUnitName) . '" '
                    . 'data-conversion="' . ($ingredient->conversion_value ?? 0) . '" '
                    . 'data-purchase_price="' . number_format($ingredient->purchase_price, 2) . '" '
                    . 'data-stock="' . number_format($ingredient->stock, 2) . '" '
                    . 'data-details="' . e($ingredient->details ?? '-') . '" '
                    . 'data-picture="' . e($pictureUrl ?? '') . '" '
                    . 'data-status="' . e($ingredient->status) . '" '
                    . 'data-bs-toggle="modal" data-bs-target="#viewIngredientModal">'
                    . '<i class="bx bx-show"></i>'
                    . '</button>';

                if ($canEdit) {
                    $actionsHtml .= '<button type="button" title="Edit" class="action-btn action-btn-edit edit-ingredient-btn" '
                        . 'data-id="' . $ingredient->id . '" '
                        . 'data-name="' . $ingName . '" '
                        . 'data-store_id="' . $ingredient->store_id . '" '
                        . 'data-buying_unit_id="' . $ingredient->buying_unit_id . '" '
                        . 'data-usage_unit_id="' . $ingredient->usage_unit_id . '" '
                        . 'data-conversion_value="' . $ingredient->conversion_value . '" '
                        . 'data-purchase_price="' . $ingredient->purchase_price . '" '
                        . 'data-stock="' . $ingredient->stock . '" '
                        . 'data-details="' . e($ingredient->details ?? '') . '" '
                        . 'data-picture="' . e($pictureUrl ?? '') . '" '
                        . 'data-status="' . e($ingredient->status) . '" '
                        . 'data-bs-toggle="modal" data-bs-target="#ingredientModal">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }

                if ($canDelete) {
                    $actionsHtml .= '<button type="button" title="Delete" class="action-btn action-btn-delete delete-ingredient-ajax-btn" '
                        . 'data-url="' . route('ingredients.delete', $ingredient->id) . '" '
                        . 'data-name="' . $ingName . '">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => $rowIndex,
                    'ingredient' => $ingHtml,
                    'buying_unit' => '<span class="text-dark fw-medium">' . e($buyingUnitName) . '</span>',
                    'usage_unit' => '<span class="text-dark fw-medium">' . e($usageUnitName) . '</span>',
                    'purchase_price' => '<span class="text-dark fw-medium">' . number_format($ingredient->purchase_price, 2) . '</span>',
                    'stock' => '<span class="text-dark fw-bold">' . number_format($ingredient->stock, 2) . '</span>',
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
            Log::error('Ingredient DataTables Ajax Error: ' . $e->getMessage());
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
            'name' => 'required|string|max:150',
            'store_id' => 'required|exists:cafe_stores,id',
            'buying_unit_id' => 'required|exists:cafe_units,id',
            'usage_unit_id' => 'required|exists:cafe_units,id',
            'conversion_value' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'details' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
        ]);

        try {
            $data = $request->except(['picture', 'stock']);
            $data['conversion_value'] = $request->input('conversion_value') ?? 0;
            $data['purchase_price'] = $request->input('purchase_price') ?? 0;
            $data['stock'] = 0;
            $data['details'] = $request->input('details') ?? '';

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            if ($request->hasFile('picture')) {
                $data['picture'] = $request->file('picture')->store('ingredients', 'public');
            } else {
                $data['picture'] = null;
            }

            $data['created_by'] = auth()->id() ?? 1;
            $data['modified_by'] = auth()->id() ?? 1;
            $data['modified_at'] = now();

            Ingredient::create($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Ingredient created successfully.']);
            }
            return redirect()->route('ingredients.index')->with('success', 'Ingredient created successfully.');
        } catch (\Exception $e) {
            Log::error('Ingredient Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create ingredient. ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to create ingredient. ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'store_id' => 'required|exists:cafe_stores,id',
            'buying_unit_id' => 'required|exists:cafe_units,id',
            'usage_unit_id' => 'required|exists:cafe_units,id',
            'conversion_value' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'details' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'status' => 'required',
        ]);

        try {
            $ingredient = Ingredient::findOrFail($id);
            $data = $request->except(['picture', 'stock']);
            $data['conversion_value'] = $request->input('conversion_value') ?? 0;
            $data['purchase_price'] = $request->input('purchase_price') ?? 0;
            $data['details'] = $request->input('details') ?? '';

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = in_array($status, ['A', 'ACTIVE', '1']) ? 'A' : 'I';

            if ($request->hasFile('picture')) {
                if ($ingredient->picture && Storage::disk('public')->exists($ingredient->picture)) {
                    Storage::disk('public')->delete($ingredient->picture);
                }
                $data['picture'] = $request->file('picture')->store('ingredients', 'public');
            }

            $data['modified_by'] = auth()->id() ?? 1;
            $data['modified_at'] = now();

            $ingredient->update($data);

            // Automatically recalculate recipe costs for all foods using this ingredient
            Ingredient::updateFoodCostPrices($ingredient->id, $data['purchase_price']);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Ingredient updated successfully.']);
            }
            return redirect()->route('ingredients.index')->with('success', 'Ingredient updated successfully.');
        } catch (\Exception $e) {
            Log::error('Ingredient Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update ingredient. ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to update ingredient. ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $ingredient = Ingredient::findOrFail($id);

            if ($ingredient->picture && Storage::disk('public')->exists($ingredient->picture)) {
                Storage::disk('public')->delete($ingredient->picture);
            }

            $ingredient->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Ingredient deleted successfully.']);
            }

            return redirect()->route('ingredients.index')->with('success', 'Ingredient deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Ingredient Delete Error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to delete ingredient: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete ingredient.');
        }
    }
}
