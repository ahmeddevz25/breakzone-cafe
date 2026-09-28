<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:brand management')->only('index');
        $this->middleware('permission:brand add')->only('store');
        $this->middleware('permission:brand edit')->only('update');
        $this->middleware('permission:brand delete')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->getBrandsDataAjax($request);
        }

        return view('admin.brands.index');
    }

    public function getBrandsDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = $request->input('search.value');
            $statusFilter = $request->input('status_filter');

            $recordsTotal = Brand::count();

            $query = Brand::query();

            if (!empty($statusFilter)) {
                $query->where('status', $statusFilter);
            }

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%");

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

            $brands = $query->get();

            $user = auth()->user();
            $canEdit = $user ? $user->can('brand edit') : true;
            $canDelete = $user ? $user->can('brand delete') : true;

            $data = [];
            foreach ($brands as $key => $brand) {
                $rowIndex = $start + $key + 1;
                $name = e($brand->name ?? '');

                $statusVal = strtoupper(trim($brand->status ?? ''));
                if ($statusVal === 'A' || $statusVal === 'ACTIVE' || $statusVal === '1') {
                    $statusHtml = '<span class="badge bg-success">Active</span>';
                } else {
                    $statusHtml = '<span class="badge bg-danger">Inactive</span>';
                }

                $actionsHtml = '<div class="table-actions justify-content-center">';
                if ($canEdit) {
                    $actionsHtml .= '<button type="button" title="Edit" class="action-btn action-btn-edit edit-brand-btn" '
                        . 'data-id="' . $brand->id . '" '
                        . 'data-name="' . $name . '" '
                        . 'data-position="' . e($brand->position ?? 0) . '" '
                        . 'data-status="' . e($brand->status ?? 'A') . '">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }

                if ($canDelete) {
                    $actionsHtml .= '<button type="button" title="Delete" class="action-btn action-btn-delete delete-brand-ajax-btn" '
                        . 'data-url="' . route('brands.delete', $brand->id) . '" '
                        . 'data-name="' . $name . '">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => '<span class="text-dark fw-medium">' . $rowIndex . '</span>',
                    'name' => '<span class="fw-bold text-dark">' . $name . '</span>',
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
            Log::error('Brand AJAX Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load brand data.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $data = $request->all();
            $data['position'] = $data['position'] ?? 0;
            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Brand::create($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Brand created successfully.']);
            }
            return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
        } catch (\Exception $e) {
            Log::error('Brand Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create brand. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to create brand. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $brand = Brand::findOrFail($id);
            $data = $request->all();
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $brand->update($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Brand updated successfully.']);
            }
            return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
        } catch (\Exception $e) {
            Log::error('Brand Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update brand. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to update brand. Please try again.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => 'Brand deleted successfully.'
                ]);
            }
            return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Brand Delete Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Failed to delete brand. Please try again.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete brand. Please try again.');
        }
    }
}
