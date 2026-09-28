<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:role management')->only('index');
        $this->middleware('permission:role add')->only('create', 'store');
        $this->middleware('permission:role edit')->only('edit', 'update');
        $this->middleware('permission:role delete')->only('destroy');
    }


    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->getRolesDataAjax($request);
        }

        // Fetch roles and their permissions
        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('name')->get();

        // Actions like add, edit, delete, etc.
        $actions = ['add', 'edit', 'delete', 'view', 'create', 'update', 'store', 'destroy'];

        // Normalizer for permission names
        $norm = function (string $s) {
            $s = strtolower($s);
            $s = preg_replace('/\s+/', ' ', $s);
            return trim($s);
        };

        // Base permissions (those without actions like "categories")
        $baseNames = [];
        foreach ($permissions as $p) {
            $n = $norm($p->name);
            if (!preg_match('/\s+(?:' . implode('|', $actions) . ')$/', $n)) {
                $baseNames[$n] = true;
            }
        }

        // Group permissions by derived base (parent -> child logic)
        $groupedPermissions = $permissions->groupBy(function ($p) use ($norm, $baseNames, $actions) {
            $n = $norm($p->name);

            // If it's a base permission (no actions)
            if (isset($baseNames[$n])) return $n;

            // If it's a child permission (e.g. "category add")
            if (preg_match('/^(.*)\s+(' . implode('|', $actions) . ')$/', $n, $m)) {
                $prefix = $norm($m[1]);

                // Check if it's a valid parent (base permission)
                if (isset($baseNames[$prefix])) return $prefix;

                // Fallback to plural or management
                $plural = $norm(Str::plural($prefix));
                if (isset($baseNames[$plural])) return $plural;

                $mgmt = $norm($prefix . ' management');
                if (isset($baseNames[$mgmt])) return $mgmt;

                return $prefix; // fallback to prefix
            }

            return $n; // if it's custom (unknown pattern)
        })->sortKeys();

        return view('admin.roles.show-role', compact('roles', 'groupedPermissions', 'permissions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);
            $role->syncPermissions($request->permissions ?? []);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            DB::commit();
            return redirect()->route('roles')->with('success', 'Role created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Store Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);

        $all = Permission::orderBy('name')->get();

        // common actions (zarurat ho to aur add kar sakte hain)
        $actions = ['add', 'edit', 'delete', 'view', 'create', 'update', 'store', 'destroy'];

        // normalizer
        $norm = function (string $s) {
            $s = strtolower($s);
            $s = preg_replace('/\s+/', ' ', $s);
            return trim($s);
        };

        // 1) Base names set (wo permissions jo action pe end nahi hotin)
        $baseNames = [];
        foreach ($all as $p) {
            $n = $norm($p->name);
            if (!preg_match('/\s+(?:' . implode('|', $actions) . ')$/', $n)) {
                $baseNames[$n] = true;
            }
        }

        // 2) Group by derived base (pure dynamic, no map)
        $groupedPermissions = $all->groupBy(function ($p) use ($norm, $baseNames, $actions) {
            $n = $norm($p->name);

            // already a base?
            if (isset($baseNames[$n])) return $n;

            // child? "<prefix> <action>"
            if (preg_match('/^(.*)\s+(' . implode('|', $actions) . ')$/', $n, $m)) {
                $prefix = $norm($m[1]);

                // direct: "user management add" -> "user management" (agar aisi naming hai)
                if (isset($baseNames[$prefix])) return $prefix;

                // plural match: "category add" -> "categories"
                $plural = $norm(Str::plural($prefix));
                if (isset($baseNames[$plural])) return $plural;

                // management suffix: "user add" -> "user management"
                $mgmt = $norm($prefix . ' management');
                if (isset($baseNames[$mgmt])) return $mgmt;

                // fallback: group by prefix itself
                return $prefix;
            }

            // unknown pattern → apne naam par group
            return $n;
        })->sortKeys();

        // role ke selected perms (lowercased)
        $rolePerms = $role->permissions->pluck('name')->map(fn($v) => strtolower($v))->toArray();

        return view('admin.roles.form', [
            'role' => $role,
            'groupedPermissions' => $groupedPermissions,
            'rolePerms' => $rolePerms,
        ]);
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'array',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->save();

            $role->syncPermissions($request->permissions ?? []);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            DB::commit();
            return redirect()->route('roles')->with('success', 'Role updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Update Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Role deleted successfully!'
                ]);
            }

            return redirect()->route('roles')->with('success', 'Role deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Role Delete Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete role.'
                ], 500);
            }
            return redirect()->route('roles')->with('error', 'Failed to delete role.');
        }
    }

    private function getRolesDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = trim($request->input('search.value', ''));

            $recordsTotal = Role::count();

            $query = Role::with('permissions');

            if (!empty($searchValue)) {
                $query->where('name', 'like', "%{$searchValue}%");
            }

            $recordsFiltered = $query->count();

            $query->orderBy('id', 'desc');

            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $roles = $query->get();

            $currentUser = auth()->user();
            $canEdit = $currentUser ? $currentUser->can('role edit') : true;
            $canDelete = $currentUser ? $currentUser->can('role delete') : true;

            $data = [];
            foreach ($roles as $key => $role) {
                $rowIndex = $start + $key + 1;
                $roleNameSafe = htmlspecialchars($role->name, ENT_QUOTES, 'UTF-8');
                $permNames = $role->permissions->pluck('name')->toArray();
                $permCount = count($permNames);
                $encodedPerms = htmlspecialchars(json_encode($permNames), ENT_QUOTES, 'UTF-8');

                // Permissions Column HTML
                if ($permCount > 0) {
                    $permsHtml = '<button type="button" '
                        . 'class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 view-permissions-modal-btn fw-semibold" '
                        . 'data-role="' . $roleNameSafe . '" '
                        . 'data-permissions=\'' . $encodedPerms . '\' '
                        . 'data-bs-toggle="modal" data-bs-target="#viewRolePermissionsModal">'
                        . '<i class="bx bx-shield-quarter"></i>'
                        . '<span>' . $permCount . ' Permissions</span>'
                        . '<span class="badge bg-primary text-white rounded-pill ms-1" style="font-size: 10px;">View</span>'
                        . '</button>';
                } else {
                    $permsHtml = '<span class="badge bg-label-secondary text-muted">0 Permissions</span>';
                }

                // Actions Column HTML
                $actionsHtml = '<div class="table-actions justify-content-center">';
                if ($canEdit) {
                    $actionsHtml .= '<button type="button" title="Edit" '
                        . 'class="action-btn action-btn-edit edit-role-btn" '
                        . 'data-id="' . $role->id . '" '
                        . 'data-name="' . $roleNameSafe . '" '
                        . 'data-permissions=\'' . $encodedPerms . '\' '
                        . 'data-bs-toggle="modal" data-bs-target="#addRoleModal">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }

                if ($canDelete) {
                    $actionsHtml .= '<button type="button" title="Delete" '
                        . 'class="action-btn action-btn-delete delete-role-ajax-btn" '
                        . 'data-url="' . route('roles.delete', $role->id) . '" '
                        . 'data-name="' . $roleNameSafe . '">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => $rowIndex,
                    'name' => '<span class="fw-bold text-dark">' . $roleNameSafe . '</span>',
                    'permissions' => $permsHtml,
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
            Log::error('Roles DataTable Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading roles data'
            ], 500);
        }
    }
}
