<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:store management')->only('index');
        $this->middleware('permission:store add')->only('store');
        $this->middleware('permission:store edit')->only('update');
        $this->middleware('permission:store delete')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->getStoresDataAjax($request);
        }

        return view('admin.store.index');
    }

    public function getStoresDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = $request->input('search.value');
            $statusFilter = $request->input('status_filter');

            // Total records before filtering
            $recordsTotal = Store::count();

            $query = Store::query();

            // Custom Status Filter
            if (!empty($statusFilter)) {
                $query->where('status', $statusFilter);
            }

            // Global Search Filter
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('store', 'like', "%{$searchValue}%")
                      ->orWhere('printer_ip_address', 'like', "%{$searchValue}%")
                      ->orWhere('printer_port', 'like', "%{$searchValue}%");

                    if (stripos('Active', $searchValue) !== false) {
                        $q->orWhere('status', 'A');
                    } elseif (stripos('Inactive', $searchValue) !== false) {
                        $q->orWhere('status', 'I');
                    }
                });
            }

            $recordsFiltered = $query->count();

            // Ordering (if enabled or default id asc)
            $query->orderBy('id', 'asc');

            // Pagination (skip and take)
            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $stores = $query->get();

            $user = auth()->user();
            $canEdit = $user ? $user->can('store edit') : true;
            $canDelete = $user ? $user->can('store delete') : true;

            $data = [];
            foreach ($stores as $key => $store) {
                $rowIndex = $start + $key + 1;
                $storeName = e($store->store ?? $store->name ?? '');
                $ip = $store->printer_ip_address ?? $store->printer_ip;
                $port = $store->printer_port;

                if ($ip) {
                    $printerHtml = '<span class="font-monospace fw-medium text-dark">' . e($ip) . ($port ? ':' . e($port) : '') . '</span>';
                } else {
                    $printerHtml = '<span class="text-secondary small fw-medium">N/A</span>';
                }

                $statusVal = strtoupper(trim($store->status ?? ''));
                if ($statusVal === 'A' || $statusVal === 'ACTIVE' || $statusVal === '1') {
                    $statusHtml = '<span class="badge bg-success">Active</span>';
                } else {
                    $statusHtml = '<span class="badge bg-danger">Inactive</span>';
                }

                $actionsHtml = '<div class="table-actions justify-content-center">';
                if ($canEdit) {
                    $actionsHtml .= '<button type="button" title="Edit" class="action-btn action-btn-edit edit-store-btn" '
                        . 'data-id="' . $store->id . '" '
                        . 'data-store="' . $storeName . '" '
                        . 'data-ip="' . e($ip ?? '') . '" '
                        . 'data-port="' . e($port ?? '') . '" '
                        . 'data-balance="' . e($store->opening_balance ?? 0) . '" '
                        . 'data-position="' . e($store->position ?? 0) . '" '
                        . 'data-status="' . e($store->status ?? 'A') . '">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }
                if ($canDelete) {
                    $actionsHtml .= '<button type="button" title="Delete" class="action-btn action-btn-delete delete-store-ajax-btn" '
                        . 'data-url="' . route('stores.delete', $store->id) . '" '
                        . 'data-name="' . $storeName . '">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => '<span class="text-dark fw-medium">' . $rowIndex . '</span>',
                    'DT_RowIndex' => '<span class="text-dark fw-medium">' . $rowIndex . '</span>',
                    'store' => '<span class="fw-bold text-dark">' . $storeName . '</span>',
                    'printer_ip' => $printerHtml,
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
            Log::error('Store AJAX Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load store data.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'store' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'printer_ip_address' => 'nullable|string|max:20',
            'printer_ip' => 'nullable|string|max:20',
            'printer_port' => 'nullable|string|max:5',
            'opening_balance' => 'nullable|numeric',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $data = $request->all();
            $data['store'] = $data['store'] ?? $data['name'] ?? '';
            $data['printer_ip_address'] = $data['printer_ip_address'] ?? $data['printer_ip'] ?? null;
            $data['company_id'] = $data['company_id'] ?? 0;
            $data['opening_balance'] = $data['opening_balance'] ?? 0;
            $data['position'] = $data['position'] ?? 0;

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Store::create($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Store created successfully.']);
            }
            return redirect()->route('stores.index')->with('success', 'Store created successfully.');
        } catch (\Exception $e) {
            Log::error('Store Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create store. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to create store. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'store' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'printer_ip_address' => 'nullable|string|max:20',
            'printer_ip' => 'nullable|string|max:20',
            'printer_port' => 'nullable|string|max:5',
            'opening_balance' => 'nullable|numeric',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {

            $store = Store::findOrFail($id);
            $data = $request->all();
            if (isset($data['name']) && !isset($data['store'])) {
                $data['store'] = $data['name'];
            }
            if (isset($data['printer_ip']) && !isset($data['printer_ip_address'])) {
                $data['printer_ip_address'] = $data['printer_ip'];
            }
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $store->update($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Store updated successfully.']);
            }
            return redirect()->route('stores.index')->with('success', 'Store updated successfully.');
        } catch (\Exception $e) {
            Log::error('Store Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update store. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to update store. Please try again.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $store = Store::findOrFail($id);
            $store->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => 'Store deleted successfully.'
                ]);
            }
            return redirect()->route('stores.index')->with('success', 'Store deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Store Delete Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Failed to delete store. Please try again.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete store. Please try again.');
        }
    }
}
