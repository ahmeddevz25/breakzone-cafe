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
        return view('admin.users-managment.show-users', compact('users', 'roles', 'schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'school_name' => 'nullable|string|max:255',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'school_name' => $request->school_name,
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
            'school_name' => 'nullable|string|max:255',
            'password' => 'nullable|min:6',
            'role' => 'required|exists:roles,name',
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            
            $data = $request->only('name', 'email', 'school_name');
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
