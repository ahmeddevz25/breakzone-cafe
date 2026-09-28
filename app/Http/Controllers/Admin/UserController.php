<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:user management')->only('index');
        $this->middleware('permission:user add')->only('store');
        $this->middleware('permission:user edit')->only('update');
        $this->middleware('permission:user delete')->only('destroy');
    }


    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->getUsersDataAjax($request);
        }

        $roles = Role::all(); // Spatie Role model
        
        $schools = Schema::hasTable('cafe_schools')
            ? DB::table('cafe_schools')->select('id', 'name')->whereNotNull('name')->orderBy('name')->get()
            : collect();

        $stores = Schema::hasTable('cafe_stores')
            ? DB::table('cafe_stores')->select('id', 'store')->orderBy('store')->get()
            : collect();

        $schoolsMap = $schools->pluck('name', 'id')->toArray();
        $storesMap = $stores->pluck('store', 'id')->toArray();

        return view('admin.users-managment.show-users', compact('roles', 'schools', 'stores', 'schoolsMap', 'storesMap'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cafe_school_id' => 'required|array|min:1',
            'store_id' => 'required|array|min:1',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name',
        ], [
            'cafe_school_id.required' => 'Please select at least one school.',
            'cafe_school_id.min' => 'Please select at least one school.',
            'store_id.required' => 'Please select at least one store.',
            'store_id.min' => 'Please select at least one store.',
        ]);

        DB::beginTransaction();
        try {
            $schoolIds = $request->input('cafe_school_id', []);
            $schoolData = (!empty($schoolIds) && is_array($schoolIds)) ? json_encode(array_values(array_map('intval', $schoolIds))) : null;

            $storeIds = $request->input('store_id', []);
            $storeData = (!empty($storeIds) && is_array($storeIds)) ? json_encode(array_values(array_map('intval', $storeIds))) : null;

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cafe_school_id' => $schoolData,
                'store_id' => $storeData,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);
            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'User created successfully.']);
            }
            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User Create Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to create user.'], 500);
            }
            return back()->with('error', 'Failed to create user.');
        }
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'cafe_school_id' => 'required|array|min:1',
            'store_id' => 'required|array|min:1',
            'password' => 'nullable|min:6',
            'role' => 'required|exists:roles,name',
        ], [
            'cafe_school_id.required' => 'Please select at least one school.',
            'cafe_school_id.min' => 'Please select at least one school.',
            'store_id.required' => 'Please select at least one store.',
            'store_id.min' => 'Please select at least one store.',
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            
            $schoolIds = $request->input('cafe_school_id', []);
            $schoolData = (!empty($schoolIds) && is_array($schoolIds)) ? json_encode(array_values(array_map('intval', $schoolIds))) : null;

            $storeIds = $request->input('store_id', []);
            $storeData = (!empty($storeIds) && is_array($storeIds)) ? json_encode(array_values(array_map('intval', $storeIds))) : null;

            $data = $request->only('name', 'email');
            $data['cafe_school_id'] = $schoolData;
            $data['store_id'] = $storeData;
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            
            $user->update($data);

            $user->syncRoles([$request->role]);
            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => true, 'message' => 'User updated successfully.']);
            }
            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User Update Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Failed to update user.'], 500);
            }
            return back()->with('error', 'Failed to update user.');
        }
    }


    public function destroy(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'User deleted successfully.'
                ]);
            }
            return redirect()->back()->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('User Delete Error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete user.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete user.');
        }
    }

    private function getUsersDataAjax(Request $request)
    {
        try {
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchValue = trim($request->input('search.value', ''));

            $recordsTotal = User::count();

            $query = User::with('roles');

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('email', 'like', "%{$searchValue}%")
                      ->orWhereHas('roles', function ($rq) use ($searchValue) {
                          $rq->where('name', 'like', "%{$searchValue}%");
                      });
                });
            }

            if ($request->filled('role_filter')) {
                $roleFilter = $request->input('role_filter');
                $query->whereHas('roles', function ($rq) use ($roleFilter) {
                    $rq->where('name', $roleFilter);
                });
            }

            $recordsFiltered = $query->count();

            $query->orderBy('id', 'desc');

            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $users = $query->get();

            $schools = Schema::hasTable('cafe_schools')
                ? DB::table('cafe_schools')->select('id', 'name')->whereNotNull('name')->pluck('name', 'id')->toArray()
                : [];

            $stores = Schema::hasTable('cafe_stores')
                ? DB::table('cafe_stores')->select('id', 'store')->pluck('store', 'id')->toArray()
                : [];

            $currentUser = auth()->user();
            $canEdit = $currentUser ? $currentUser->can('user edit') : true;
            $canDelete = $currentUser ? $currentUser->can('user delete') : true;

            $data = [];
            foreach ($users as $key => $user) {
                $rowIndex = $start + $key + 1;
                $userSchoolIds = $user->cafe_school_ids;
                $schoolCount = count($userSchoolIds);
                $userSchoolNames = array_values(array_filter(array_map(fn($id) => $schools[$id] ?? null, $userSchoolIds)));

                $userStoreIds = $user->store_ids;
                $storeCount = count($userStoreIds);
                $userStoreNames = array_values(array_filter(array_map(fn($id) => $stores[$id] ?? null, $userStoreIds)));

                $roleName = $user->roles->first()->name ?? 'No Role';
                $encodedSchools = htmlspecialchars(json_encode($userSchoolNames), ENT_QUOTES, 'UTF-8');
                $encodedStores = htmlspecialchars(json_encode($userStoreNames), ENT_QUOTES, 'UTF-8');
                $encodedSchoolIds = htmlspecialchars(json_encode($userSchoolIds), ENT_QUOTES, 'UTF-8');
                $encodedStoreIds = htmlspecialchars(json_encode($userStoreIds), ENT_QUOTES, 'UTF-8');
                $userNameSafe = htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8');
                $userEmailSafe = htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8');
                $roleNameSafe = htmlspecialchars($roleName, ENT_QUOTES, 'UTF-8');

                // Schools HTML
                if ($schoolCount > 0) {
                    if ($schoolCount === 1) {
                        $schName = $userSchoolNames[0] ?? ('School #' . $userSchoolIds[0]);
                        $schoolsHtml = '<span class="badge bg-label-info text-dark border fw-bold">' . htmlspecialchars($schName, ENT_QUOTES, 'UTF-8') . '</span>';
                    } else {
                        $schoolsHtml = '<span class="badge bg-label-primary text-dark border fw-bold viewDetailBtn" '
                            . 'style="cursor: pointer;" title="Click to view details" '
                            . 'data-name="' . $userNameSafe . '" '
                            . 'data-email="' . $userEmailSafe . '" '
                            . 'data-role="' . $roleNameSafe . '" '
                            . 'data-schools=\'' . $encodedSchools . '\' '
                            . 'data-stores=\'' . $encodedStores . '\' '
                            . 'data-bs-toggle="modal" data-bs-target="#userDetailModal">'
                            . '<i class="bx bxs-school me-1"></i> ' . $schoolCount . ' Schools</span>';
                    }
                } else {
                    $schoolsHtml = '<span class="text-muted small">N/A</span>';
                }

                // Stores HTML
                if ($storeCount > 0) {
                    if ($storeCount === 1) {
                        $stName = $userStoreNames[0] ?? ('Store #' . $userStoreIds[0]);
                        $storesHtml = '<span class="badge bg-label-warning text-dark border fw-bold">' . htmlspecialchars($stName, ENT_QUOTES, 'UTF-8') . '</span>';
                    } else {
                        $storesHtml = '<span class="badge bg-label-secondary text-dark border fw-bold viewDetailBtn" '
                            . 'style="cursor: pointer;" title="Click to view details" '
                            . 'data-name="' . $userNameSafe . '" '
                            . 'data-email="' . $userEmailSafe . '" '
                            . 'data-role="' . $roleNameSafe . '" '
                            . 'data-schools=\'' . $encodedSchools . '\' '
                            . 'data-stores=\'' . $encodedStores . '\' '
                            . 'data-bs-toggle="modal" data-bs-target="#userDetailModal">'
                            . '<i class="bx bx-store-alt me-1"></i> ' . $storeCount . ' Stores</span>';
                    }
                } else {
                    $storesHtml = '<span class="text-muted small">N/A</span>';
                }

                // Roles HTML
                $rolesHtml = '';
                foreach ($user->getRoleNames() as $r) {
                    $rolesHtml .= '<span class="badge bg-primary me-1">' . htmlspecialchars($r, ENT_QUOTES, 'UTF-8') . '</span>';
                }
                if (empty($rolesHtml)) {
                    $rolesHtml = '<span class="text-muted small">No Role</span>';
                }

                // Actions HTML
                $actionsHtml = '<div class="table-actions justify-content-center">';
                $actionsHtml .= '<button type="button" class="action-btn action-btn-view viewDetailBtn" '
                    . 'title="View Details" '
                    . 'data-name="' . $userNameSafe . '" '
                    . 'data-email="' . $userEmailSafe . '" '
                    . 'data-role="' . $roleNameSafe . '" '
                    . 'data-schools=\'' . $encodedSchools . '\' '
                    . 'data-stores=\'' . $encodedStores . '\' '
                    . 'data-bs-toggle="modal" data-bs-target="#userDetailModal">'
                    . '<i class="bx bx-show"></i>'
                    . '</button>';

                if ($canEdit) {
                    $actionsHtml .= '<button type="button" class="action-btn action-btn-edit editUserBtn" '
                        . 'title="Edit User" '
                        . 'data-id="' . $user->id . '" '
                        . 'data-name="' . $userNameSafe . '" '
                        . 'data-email="' . $userEmailSafe . '" '
                        . 'data-schools=\'' . $encodedSchoolIds . '\' '
                        . 'data-stores=\'' . $encodedStoreIds . '\' '
                        . 'data-role="' . $roleNameSafe . '" '
                        . 'data-bs-toggle="modal" data-bs-target="#userModal">'
                        . '<i class="bx bx-edit"></i>'
                        . '</button>';
                }

                if ($canDelete) {
                    $actionsHtml .= '<button type="button" class="action-btn action-btn-delete delete-user-ajax-btn" '
                        . 'data-url="' . route('users.destroy', $user->id) . '" '
                        . 'data-name="' . $userNameSafe . '" '
                        . 'title="Delete User">'
                        . '<i class="bx bx-trash"></i>'
                        . '</button>';
                }
                $actionsHtml .= '</div>';

                $data[] = [
                    'index' => $rowIndex,
                    'name' => '<span class="fw-bold text-dark">' . $userNameSafe . '</span>',
                    'email' => '<span class="text-dark fw-medium">' . $userEmailSafe . '</span>',
                    'schools' => $schoolsHtml,
                    'stores' => $storesHtml,
                    'roles' => $rolesHtml,
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
            Log::error('Users DataTable Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading users data'
            ], 500);
        }
    }
}
