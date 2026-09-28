<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:unit management')->only('index');
        $this->middleware('permission:unit add')->only('store');
        $this->middleware('permission:unit edit')->only('update');
        $this->middleware('permission:unit delete')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->getUnitsDataAjax($request);
        }

        return view('admin.units.index');
    }

    public function getUnitsDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = $request->input('search.value');
            $statusFilter = $request->input('status_filter');

            $recordsTotal = Unit::count();

            $query = Unit::query();

            if (!empty($statusFilter)) {
                $query->where('status', $statusFilter);
            }

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('unit', 'like', "%{$searchValue}%");

                    if (stripos('Active', $searchValue) !== false) {
                        $q->orWhere('status', 'A');
                    } elseif (stripos('Inactive', $searchValue) !== false) {
                        $q->orWhere('status', 'I');
                    }
                });
            }

            $recordsFiltered = $query->count();

            $query->orderBy('position', 'asc')->orderBy('id', 'asc');

            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $units = $query->get();

            $user = auth()->user();
            $canEdit = $user ? $user->can('unit edit') : true;
            $canDelete = $user ? $user->can('unit delete') : true;

            $data = [];
            foreach ($units as $key => $unit) {
                $rowIndex = $start + $key + 1;
                $unitName = e($unit->unit ?? $unit->name ?? '');
                $quantity = e($unit->quantity ?? 0);

                $statusVal = strtoupper(trim($unit->status ?? ''));
                if ($statusVal === 'A' || $statusVal === 'ACTIVE' || $statusVal === '1') {
                    $statusHtml = '<span class="badge bg-success">Active</span>';
                } else {
                    $statusHtml = '<span class="badge bg-danger">Inactive</span>';
                }

                $actionsHtml = '<div class="table-actions justify-content-center">';
                if ($canEdit) {
                    $actionsHtml .= '<button type="button" title="Edit" class="action-btn action-btn-edit edit-unit-btn" '
                        . 'data-id="' . $unit->id . '" '
                        . 'data-unit="' . $unitName . '" '
                        . 'data-quantity="' . $quantity . '" '
                        . 'data-position="' . e($unit->position ?? 0) . '" '
                        . 'data-status="' . e($unit->status ?? 'A') . '">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }

                if ($canDelete) {
                    $actionsHtml .= '<button type="button" title="Delete" class="action-btn action-btn-delete delete-unit-ajax-btn" '
                        . 'data-url="' . route('units.delete', $unit->id) . '" '
                        . 'data-name="' . $unitName . '">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => '<span class="text-dark fw-medium">' . $rowIndex . '</span>',
                    'unit' => '<span class="fw-bold text-dark">' . $unitName . '</span>',
                    'quantity' => '<span class="text-dark fw-medium">' . $quantity . '</span>',
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
            Log::error('Unit AJAX Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load unit data.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'quantity' => 'nullable|numeric',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $data = $request->all();
            $data['unit'] = $data['unit'] ?? $data['name'] ?? '';
            $data['quantity'] = $data['quantity'] ?? 0;
            $data['position'] = $data['position'] ?? 0;

            $status = strtoupper(trim($request->status ?? 'A'));
            $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';

            Unit::create($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Unit created successfully.']);
            }
            return redirect()->route('units.index')->with('success', 'Unit created successfully.');
        } catch (\Exception $e) {
            Log::error('Unit Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create unit. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to create unit. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'unit' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'quantity' => 'nullable|numeric',
            'position' => 'nullable|integer',
            'status' => 'required',
        ]);

        try {
            $unit = Unit::findOrFail($id);
            $data = $request->all();
            if (isset($data['name']) && !isset($data['unit'])) {
                $data['unit'] = $data['name'];
            }
            if (isset($data['status'])) {
                $status = strtoupper(trim($data['status']));
                $data['status'] = ($status === 'ACTIVE' || $status === '1' || $status === 'A') ? 'A' : 'I';
            }
            $unit->update($data);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Unit updated successfully.']);
            }
            return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
        } catch (\Exception $e) {
            Log::error('Unit Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update unit. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'Failed to update unit. Please try again.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => 'Unit deleted successfully.'
                ]);
            }
            return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Unit Delete Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Failed to delete unit. Please try again.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete unit. Please try again.');
        }
    }
}
