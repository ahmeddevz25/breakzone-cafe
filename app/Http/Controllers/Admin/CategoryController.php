<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category management')->only('index');
        $this->middleware('permission:category add')->only('store');
        $this->middleware('permission:category edit')->only('update');
        $this->middleware('permission:category delete')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->getCategoriesDataAjax($request);
        }

        try {
            $categories = Category::with('parent')->orderBy('id', 'asc')->get();
            return view('admin.categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Category Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while fetching categories.');
        }
    }

    public function getCategoriesDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = $request->input('search.value');
            $statusFilter = $request->input('status_filter');

            $recordsTotal = Category::count();

            $query = Category::with('parent');

            if (!empty($statusFilter)) {
                $query->where('status', $statusFilter);
            }

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('category', 'like', "%{$searchValue}%");

                    if (stripos('Active', $searchValue) !== false) {
                        $q->orWhere('status', 'A');
                    } elseif (stripos('Inactive', $searchValue) !== false) {
                        $q->orWhere('status', 'I');
                    }
                });
            }

            $recordsFiltered = $query->count();

            $query->orderBy('id', 'asc');

            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $categories = $query->get();

            $user = auth()->user();
            $canEdit = $user ? $user->can('category edit') : true;
            $canDelete = $user ? $user->can('category delete') : true;

            $data = [];
            foreach ($categories as $key => $category) {
                $rowIndex = $start + $key + 1;
                $catName = e($category->category ?? $category->name ?? '');

                $statusVal = strtoupper(trim($category->status ?? ''));
                if ($statusVal === 'A' || $statusVal === 'ACTIVE' || $statusVal === '1') {
                    $statusHtml = '<span class="badge bg-success">Active</span>';
                } else {
                    $statusHtml = '<span class="badge bg-danger">Inactive</span>';
                }

                $actionsHtml = '<div class="table-actions justify-content-center">';
                if ($canEdit) {
                    $actionsHtml .= '<button type="button" title="Edit" class="action-btn action-btn-edit edit-category-btn" '
                        . 'data-id="' . $category->id . '" '
                        . 'data-category="' . $catName . '" '
                        . 'data-parent_id="' . ($category->parent_id ?? '') . '" '
                        . 'data-position="' . e($category->position ?? 0) . '" '
                        . 'data-status="' . e($category->status ?? 'A') . '">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }

                if ($canDelete) {
                    $actionsHtml .= '<button type="button" title="Delete" class="action-btn action-btn-delete delete-category-ajax-btn" '
                        . 'data-url="' . route('categories.delete', $category->id) . '" '
                        . 'data-name="' . $catName . '">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => '<span class="text-dark fw-medium">' . $rowIndex . '</span>',
                    'category' => '<span class="fw-bold text-dark">' . $category->full_path . '</span>',
                    'status' => $statusHtml,
                    'actions' => $actionsHtml,
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Category AJAX Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load category data.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:cafe_categories,id',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $data = $request->all();
            $data['category'] = $data['category'] ?? $data['name'] ?? '';
            $data['position'] = $data['position'] ?? 0;
            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Category::create($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Category created successfully.']);
            }
            return redirect()->route('categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Category Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create category. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to create category. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:cafe_categories,id',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $category = Category::findOrFail($id);
            // Prevent category from being its own parent
            if ($request->parent_id == $category->id) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['status' => false, 'message' => 'Category cannot be its own parent.'], 422);
                }
                return redirect()->back()->with('error', 'Category cannot be its own parent.');
            }
            $data = $request->all();
            if (isset($data['name']) && !isset($data['category'])) {
                $data['category'] = $data['name'];
            }
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $category->update($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Category updated successfully.']);
            }
            return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Category Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update category. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to update category. Please try again.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete(); // This will cascade delete children if DB constraint is set, or we can handle it here if not. The migration has onDelete cascade.
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => 'Category deleted successfully.'
                ]);
            }
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Category Delete Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Failed to delete category. Please try again.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete category. Please try again.');
        }
    }
}
