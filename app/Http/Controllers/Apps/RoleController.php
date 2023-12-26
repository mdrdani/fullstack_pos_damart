<?php

namespace App\Http\Controllers\Apps;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    //
    public function index()
    {
        //get roles
        $roles = Role::when(request()->q, function ($roles) {
            $roles = $roles->where('name', 'like', '%' . request()->q . '%');
        })->with('permissions')->latest()->paginate(5);

        //return inertia view
        return inertia('Apps/Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        //get permissions
        $permissions = Permission::all();

        //return inertia view
        return inertia('Apps/Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'permissions' => 'required'
        ]);

        // create role
        $role = Role::create(['name' => $request->name]);

        // assign permissions
        $role->givePermissionTo($request->permissions);

        // redirect
        return redirect()->route('apps.roles.index');
    }

    public function edit($id)
    {
        //get role
        $role = Role::with('permissions')->findOrFail($id);

        //get permissions
        $permissions = Permission::all();

        //return inertia view
        return inertia('Apps/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $this->validate($request, [
            'name' => 'required',
            'permissions' => 'required'
        ]);

        // update role
        $role->update(['name' => $request->name]);

        // sync permissions
        $role->syncPermissions($request->permissions);

        // redirect
        return redirect()->route('apps.roles.index');
    }

    public function destroy($id)
    {
        //get role
        $role = Role::findOrFail($id);

        //delete role
        $role->delete();

        //redirect
        return redirect()->route('apps.roles.index');
    }
}
