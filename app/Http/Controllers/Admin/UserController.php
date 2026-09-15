<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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


    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all(); // Spatie Role model
        $schools = DB::table('cafe_schools')->select('id', 'name')->whereNotNull('name')->orderBy('name')->get();
        $stores = DB::table('cafe_stores')->select('id', 'store')->orderBy('store')->get();

        $schoolsMap = $schools->pluck('name', 'id')->toArray();
        $storesMap = $stores->pluck('store', 'id')->toArray();

        return view('admin.users-managment.show-users', compact('users', 'roles', 'schools', 'stores', 'schoolsMap', 'storesMap'));
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

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User Create Error: ' . $e->getMessage());
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

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User Update Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update user.');
        }
    }


    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
