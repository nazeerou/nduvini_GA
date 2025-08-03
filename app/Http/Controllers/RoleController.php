<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Module;
use Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('is_active', true)
                 ->get();

        return view('roles.create', compact('roles'));
    }

    
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    Role::create([
        'name' => $request->name,
        'description' => $request->description,
        'guard_name' => $request->guard_name ?? 'web',
        'is_active' => $request->is_active ?? 1,
    ]);

    return redirect()->back()->with('message', 'Role created successfully.');
}

public function update(Request $request, $id)
{
    $role = Role::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        'description' => 'nullable|string|max:1000',
    ]);

    $role->update([
        'name' => $request->name,
        'description' => $request->description,
    ]);

    return redirect()->back()->with('success', 'Role updated successfully.');
}

    public function edit($id)
    {
        $lims_role_data = Roles::find($id);
        return $lims_role_data;
    }


    public function changePermissions($roleId) 
    {
        $role = Role::findOrFail($roleId);

        $modules = Module::with('permissions')->get();
        $assigned_permissions = $role->permissions->pluck('id')->toArray();
    
        return view('roles.permissions', compact('role', 'modules', 'assigned_permissions'));
    }
    
    public function updatePermissions(Request $request, $roleId) 
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($request->permissions ?? []);
    
        return redirect()->back()->with('message', 'Permissions updated successfully.');
    }

    public function permission($id)
    {
        $role_data = Role::find($id);
        // $permissions = Role::findByName($role_data->name)->permissions;
        $permissions = $role_data ? $role_data->permissions : collect();
        foreach ($permissions as $permission)
            $all_permission[] = $permission->name;
        if(empty($all_permission))
            $all_permission[] = 'text';
        return view('roles.permissions', compact('role_data', 'all_permission'));
    }

    public function destroy($id)
    {
    $role = Role::findOrFail($id);
    $role->delete();

    return redirect()->back()->with('error', 'Role deleted successfully.');
    }

}
