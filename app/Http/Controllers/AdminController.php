<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.index', compact('users', 'roles', 'permissions'));
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('admin.index')->with('success', 'Role created successfully.');
    }

    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('admin.index')->with('success', 'Permission created successfully.');
    }

    public function assignRole(Request $request, User $user)
    {
        $user->assignRole($request->role);

        return redirect()->route('admin.index')->with('success', 'Role assigned successfully.');
    }

    public function assignPermission(Request $request, User $user)
    {
        $user->givePermissionTo($request->permission);

        return redirect()->route('admin.index')->with('success', 'Permission assigned successfully.');
    }
}
