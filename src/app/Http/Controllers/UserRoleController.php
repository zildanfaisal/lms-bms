<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $baseQuery = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name');

        if ($request->wantsJson()) {
            $items = $baseQuery->with('roles')->limit(20)->get()->map(function ($u) {
                $roles = $u->roles->pluck('name')->values();
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'roles' => $roles,
                    'selectedRole' => $roles->first(),
                ];
            });
            return response()->json(['items' => $items]);
        }

        $users = $baseQuery->paginate(10)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('users.roles.index', compact('users', 'roles', 'q'));
    }

    /**
     * Sync roles for a specific user.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['nullable','string','exists:roles,name'],
            'roles' => ['sometimes','array'], // backward-compat if used elsewhere
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($data['roles'] ?? []);
        } else {
            $role = $data['role'] ?? null;
            $user->syncRoles($role ? [$role] : []);
        }

        return back()->with('success', 'User roles updated');
    }

    /**
     * Precheck before updating a user's role to ensure we don't remove the last Super Admin.
     */
    public function precheck(Request $request, User $user)
    {
        $role = $request->string('role');

        // Determine current and new role sets (UI uses single select named 'role')
        $currentRoles = $user->roles->pluck('name')->values()->all();
        $newRoles = $role->isNotEmpty() ? [$role->toString()] : [];

        $removingSuperAdmin = in_array('Super Admin', $currentRoles, true) && !in_array('Super Admin', $newRoles, true);
        if ($removingSuperAdmin) {
            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
            $count = $superAdminRole ? $superAdminRole->users()->count() : 0;
            if ($count <= 1) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Tidak dapat menghapus role Super Admin dari user ini karena akan menjadi tidak ada Super Admin yang tersisa.',
                    'issues' => ['super_admin_remaining' => $count],
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'Perubahan role dapat disimpan. Lanjutkan?',
        ]);
    }
}
