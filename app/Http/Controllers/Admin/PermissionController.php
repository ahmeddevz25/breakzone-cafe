<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:permission management')->only('index');
        $this->middleware('permission:permission add')->only('store');
        $this->middleware('permission:permission edit')->only('edit', 'update');
        $this->middleware('permission:permission delete')->only('destroy');
    }


    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->getPermissionsDataAjax($request);
        }

        $permissions = Permission::all();
        return view('admin.permissions.show-permissions', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:permissions,name']);

        DB::beginTransaction();
        try {
            $permission = Permission::create(['name' => $request->name]);
            
            // Automatically assign new permission to Super Administrator
            $admin = Role::where('name', 'Super Administrator')->first();
            if ($admin) {
                $admin->givePermissionTo($permission);
            }
            
            DB::commit();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Permission created successfully!']);
            }
            return back()->with('success', 'Permission created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Permission Store Error: " . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create permission.'], 500);
            }
            return back()->with('error', 'Failed to create permission.');
        }
    }

    public function edit($id)
    {
        try {
            // dd($id/);
            $permission = Permission::findOrFail($id);
            return view('admin.permissions.form', compact('permission'));
        } catch (\Exception $e) {
            Log::error("Permission Edit Error: " . $e->getMessage());
            return redirect()->route('permissions')->with('error', 'Permission not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|unique:permissions,name,' . $id]);

        DB::beginTransaction();
        try {
            $permission = Permission::findOrFail($id);
            $permission->update(['name' => $request->name]);
            DB::commit();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'Permission updated successfully!']);
            }
            return redirect()->route('permissions')->with('success', 'Permission updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Permission Update Error: " . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update permission.'], 500);
            }
            return back()->with('error', 'Failed to update permission.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Permission deleted successfully!'
                ]);
            }

            return back()->with('success', 'Permission deleted successfully!');
        } catch (\Exception $e) {
            Log::error("Permission Delete Error: " . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete permission.'
                ], 500);
            }
            return back()->with('error', 'Failed to delete permission.');
        }
    }

    private function getPermissionsDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = trim($request->input('search.value', ''));

            $recordsTotal = Permission::count();

            $query = Permission::query();

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('guard_name', 'like', "%{$searchValue}%");
                });
            }

            $recordsFiltered = $query->count();

            $query->orderBy('id', 'desc');

            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $permissions = $query->get();

            $currentUser = auth()->user();
            $canEdit = $currentUser ? $currentUser->can('permission edit') : true;
            $canDelete = $currentUser ? $currentUser->can('permission delete') : true;

            $data = [];
            foreach ($permissions as $key => $permission) {
                $rowIndex = $start + $key + 1;
                $permNameSafe = htmlspecialchars($permission->name, ENT_QUOTES, 'UTF-8');
                $guardSafe = htmlspecialchars($permission->guard_name ?? 'web', ENT_QUOTES, 'UTF-8');

                $actionsHtml = '<div class="table-actions justify-content-center">';
                if ($canEdit) {
                    $actionsHtml .= '<button type="button" title="Edit" '
                        . 'class="action-btn action-btn-edit edit-permission-btn" '
                        . 'data-id="' . $permission->id . '" '
                        . 'data-name="' . $permNameSafe . '" '
                        . 'data-bs-toggle="modal" data-bs-target="#permissionModal">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }

                if ($canDelete) {
                    $actionsHtml .= '<button type="button" title="Delete" '
                        . 'class="action-btn action-btn-delete delete-permission-ajax-btn" '
                        . 'data-url="' . route('permissions.delete', $permission->id) . '" '
                        . 'data-name="' . $permNameSafe . '">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => $rowIndex,
                    'name' => '<span class="fw-bold text-dark">' . $permNameSafe . '</span>',
                    'guard' => '<span class="badge bg-label-info text-dark border fw-bold">' . $guardSafe . '</span>',
                    'actions' => $actionsHtml,
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Permissions DataTable Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading permissions data'
            ], 500);
        }
    }
}
