<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('name')->paginate(10)->withQueryString();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string','exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()->route('roles.index')->with('success','Role created');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $users = User::orderBy('name')->get();
        $roleUserIds = User::role($role->name)->pluck('id')->toArray();
        return view('roles.edit', compact('role','permissions','rolePermissions', 'users', 'roleUserIds'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:roles,name,'.$role->id],
            'permissions' => ['array'],
            'permissions.*' => ['string','exists:permissions,name'],
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success','Role updated');
    }

    /**
     * Sync users assigned to this role.
     * Accepts users[] array of user ids.
     */
    public function syncUsers(Request $request, Role $role)
    {
        $data = $request->validate([
            'users' => ['nullable','array'],
            'users.*' => ['integer','exists:users,id'],
        ]);

        $selected = $data['users'] ?? [];

        // current users who have this role
        $current = User::role($role->name)->pluck('id')->toArray();

        $toAdd = array_diff($selected, $current);
        $toRemove = array_diff($current, $selected);

        if (!empty($toAdd)) {
            User::whereIn('id', $toAdd)->get()->each(function ($u) use ($role) {
                $u->assignRole($role->name);
            });
        }

        if (!empty($toRemove)) {
            User::whereIn('id', $toRemove)->get()->each(function ($u) use ($role) {
                $u->removeRole($role->name);
            });
        }

        return back()->with('success','Users synced for role');
    }

    public function destroy(Role $role)
    {
        // prevent deleting Super Admin by default
        if ($role->name === 'Super Admin') {
            return back()->with('error','Cannot delete Super Admin role');
        }
        $role->delete();
        return back()->with('success','Role deleted');
    }
}
