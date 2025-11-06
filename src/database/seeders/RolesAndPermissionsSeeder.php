<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define entities and CRUD permissions
        $entities = [
            'direktorat', 'divisi', 'unit', 'jabatan', 'posisi', 'karyawan', 'user', 'role', 'permission',
            'learning platform', 'learning target', 'learning log'
        ];

        $actions = ['view any', 'view', 'create', 'update', 'delete'];

        $allPermissions = [];
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $name = $action.' '.$entity;
                $allPermissions[] = $name;
                Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            }
        }

        // Additional actions for learning logs
        foreach (['submit learning log','approve learning log','reject learning log'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Reporting permissions
        foreach (['view learning reports company','view learning reports team'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Additional high-level permission to access role/permission settings
        $manageSettings = Permission::firstOrCreate(['name' => 'manage roles & permissions', 'guard_name' => 'web']);

        // Create roles
        $super = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        // Assign permissions
        // Super Admin: assign all known permissions + manage settings
        $super->syncPermissions(Permission::all());

        // Admin (Supervisor/Manager): can manage team approvals, view team reports
        $adminPerms = [];
        foreach (['direktorat','divisi','unit','jabatan','posisi','karyawan'] as $e) {
            foreach (['view any','view','create','update'] as $a) {
                $adminPerms[] = $a.' '.$e;
            }
            // allow delete only for non-critical (optional)
            // $adminPerms[] = 'delete '.$e;
        }
        // Learning features for Admins
        foreach (['view any learning log','view learning log','approve learning log','reject learning log'] as $p) {
            $adminPerms[] = $p;
        }
        $adminPerms[] = 'view learning reports team';
        foreach ($adminPerms as $p) {
            if ($perm = Permission::where('name',$p)->first()) {
                $admin->givePermissionTo($perm);
            }
        }

        // User: can create/submit own learning logs and view platforms
        foreach ([
            'view any karyawan','view karyawan',
            'view any learning platform','view learning platform',
            'create learning log','view learning log','update learning log','submit learning log',
        ] as $p) {
            if ($perm = Permission::where('name',$p)->first()) {
                $user->givePermissionTo($perm);
            }
        }

        // Ensure manage settings permission belongs to Super Admin by default
        $super->givePermissionTo($manageSettings);

        // HR (Super Admin) gets company-wide learning report access by syncPermissions above
    }
}
