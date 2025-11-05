<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->paginate(15)->withQueryString();
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:permissions,name'],
        ]);
        Permission::create(['name' => $data['name'], 'guard_name' => 'web']);
        return redirect()->route('permissions.index')->with('success','Permission created');
    }

    public function destroy(Permission $permission)
    {
        // avoid deleting critical permission by default
        if ($permission->name === 'manage roles & permissions') {
            return back()->with('error','Cannot delete this permission');
        }
        $permission->delete();
        return back()->with('success','Permission deleted');
    }
}
