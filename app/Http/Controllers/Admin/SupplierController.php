<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:supplier management')->only('index');
        $this->middleware('permission:supplier add')->only('store');
        $this->middleware('permission:supplier edit')->only('update');
        $this->middleware('permission:supplier delete')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->getSuppliersDataAjax($request);
        }

        return view('admin.suppliers.index');
    }

    public function getSuppliersDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = $request->input('search.value');
            $statusFilter = $request->input('status_filter');

            $recordsTotal = Supplier::count();

            $query = Supplier::query();

            // Custom Status Filter
            if (!empty($statusFilter)) {
                $query->where('status', $statusFilter);
            }

            // Global Search Filter
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('company', 'like', "%{$searchValue}%")
                      ->orWhere('mobile', 'like', "%{$searchValue}%")
                      ->orWhere('email', 'like', "%{$searchValue}%")
                      ->orWhere('ntn_no', 'like', "%{$searchValue}%");

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

            $suppliers = $query->get();

            $user = auth()->user();
            $canEdit = $user ? $user->can('supplier edit') : true;
            $canDelete = $user ? $user->can('supplier delete') : true;

            $data = [];
            foreach ($suppliers as $key => $supplier) {
                $rowIndex = $start + $key + 1;
                $name = e($supplier->name ?? '');
                $company = e($supplier->company ?? '');
                $mobile = e($supplier->mobile ?? '');
                $ntn = e($supplier->ntn_no ?? $supplier->ntn ?? '');
                $address = e($supplier->address ?? '');
                $email = e($supplier->email ?? '');

                $statusVal = strtoupper(trim($supplier->status ?? ''));
                if ($statusVal === 'A' || $statusVal === 'ACTIVE' || $statusVal === '1') {
                    $statusHtml = '<span class="badge bg-success">Active</span>';
                } else {
                    $statusHtml = '<span class="badge bg-danger">Inactive</span>';
                }

                $actionsHtml = '<div class="table-actions justify-content-center">';
                
                // View details
                $actionsHtml .= '<button type="button" title="View Details" class="action-btn action-btn-view view-supplier-btn" '
                    . 'data-name="' . $name . '" '
                    . 'data-company="' . $company . '" '
                    . 'data-address="' . $address . '" '
                    . 'data-mobile="' . $mobile . '" '
                    . 'data-ntn="' . $ntn . '" '
                    . 'data-email="' . $email . '" '
                    . 'data-status="' . e($supplier->status ?? 'A') . '">'
                    . '<i class="bx bx-show"></i>'
                    . '</button>';

                if ($canEdit) {
                    $actionsHtml .= '<button type="button" title="Edit" class="action-btn action-btn-edit edit-supplier-btn" '
                        . 'data-id="' . $supplier->id . '" '
                        . 'data-name="' . $name . '" '
                        . 'data-company="' . $company . '" '
                        . 'data-address="' . $address . '" '
                        . 'data-mobile="' . $mobile . '" '
                        . 'data-ntn="' . $ntn . '" '
                        . 'data-email="' . $email . '" '
                        . 'data-status="' . e($supplier->status ?? 'A') . '" '
                        . 'data-bs-toggle="modal" data-bs-target="#supplierModal">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }

                if ($canDelete) {
                    $actionsHtml .= '<button type="button" title="Delete" class="action-btn action-btn-delete delete-supplier-ajax-btn" '
                        . 'data-url="' . route('suppliers.delete', $supplier->id) . '" '
                        . 'data-name="' . $name . '">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => '<span class="text-dark fw-medium">' . $rowIndex . '</span>',
                    'name' => '<span class="fw-bold text-dark">' . $name . '</span>',
                    'company' => '<span class="text-dark fw-medium">' . ($company ?: 'N/A') . '</span>',
                    'mobile' => '<span class="text-dark font-monospace">' . ($mobile ?: 'N/A') . '</span>',
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
            Log::error('Supplier AJAX Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load supplier data.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:200',
            'company' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:250',
            'mobile' => ['required', 'regex:/^[0-9+\-\s()]{7,25}$/'],
            'ntn_no' => ['nullable', 'regex:/^[0-9\-]{5,20}$/'],
            'ntn' => ['nullable', 'regex:/^[0-9\-]{5,20}$/'],
            'email' => 'nullable|email|max:100|unique:cafe_suppliers,email',
            'status' => 'required',
        ], [
            'mobile.required' => 'The mobile number field is required.',
            'mobile.regex' => 'The mobile number format is invalid. Please enter numbers only (e.g., 03001234567).',
            'ntn_no.regex' => 'The NTN # format is invalid. Please enter numbers and hyphens only (e.g., 1234567-8).',
            'ntn.regex' => 'The NTN # format is invalid. Please enter numbers and hyphens only (e.g., 1234567-8).',
        ]);

        try {
            $data = $request->all();
            if (empty($data['ntn_no']) && !empty($data['ntn'])) {
                $data['ntn_no'] = $data['ntn'];
            }
            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Supplier::create($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Supplier created successfully.']);
            }
            return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            Log::error('Supplier Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create supplier. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to create supplier. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:200',
            'company' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:250',
            'mobile' => ['required', 'regex:/^[0-9+\-\s()]{7,25}$/'],
            'ntn_no' => ['nullable', 'regex:/^[0-9\-]{5,20}$/'],
            'ntn' => ['nullable', 'regex:/^[0-9\-]{5,20}$/'],
            'email' => 'nullable|email|max:100|unique:cafe_suppliers,email,' . $id,
            'status' => 'required',
        ], [
            'mobile.required' => 'The mobile number field is required.',
            'mobile.regex' => 'The mobile number format is invalid. Please enter numbers only (e.g., 03001234567).',
            'ntn_no.regex' => 'The NTN # format is invalid. Please enter numbers and hyphens only (e.g., 1234567-8).',
            'ntn.regex' => 'The NTN # format is invalid. Please enter numbers and hyphens only (e.g., 1234567-8).',
        ]);

        try {
            $supplier = Supplier::findOrFail($id);
            $data = $request->all();
            if (isset($data['ntn']) && !isset($data['ntn_no'])) {
                $data['ntn_no'] = $data['ntn'];
            }
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $supplier->update($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Supplier updated successfully.']);
            }
            return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
        } catch (\Exception $e) {
            Log::error('Supplier Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update supplier. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to update supplier. Please try again.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => 'Supplier deleted successfully.'
                ]);
            }
            return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Supplier Delete Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Failed to delete supplier. Please try again.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete supplier. Please try again.');
        }
    }
}
