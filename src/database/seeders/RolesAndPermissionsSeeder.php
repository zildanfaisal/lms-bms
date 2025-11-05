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
            'direktorat', 'divisi', 'unit', 'jabatan', 'posisi', 'karyawan', 'user', 'role', 'permission'
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

        // Additional high-level permission to access role/permission settings
        $manageSettings = Permission::firstOrCreate(['name' => 'manage roles & permissions', 'guard_name' => 'web']);

        // Create roles
        $super = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        // Assign permissions
        // Super Admin: assign all known permissions + manage settings
        $super->syncPermissions(Permission::all());

        // Admin (HR): can view/create/update on master data + karyawan, but not delete roles/permissions
        $adminPerms = [];
        foreach (['direktorat','divisi','unit','jabatan','posisi','karyawan'] as $e) {
            foreach (['view any','view','create','update'] as $a) {
                $adminPerms[] = $a.' '.$e;
            }
            // allow delete only for non-critical (optional)
            // $adminPerms[] = 'delete '.$e;
        }
        foreach ($adminPerms as $p) {
            if ($perm = Permission::where('name',$p)->first()) {
                $admin->givePermissionTo($perm);
            }
        }

        // User: basic view self-related; as a default keep minimal
        // You can expand later per business rules
        foreach (['view any karyawan','view karyawan'] as $p) {
            if ($perm = Permission::where('name',$p)->first()) {
                $user->givePermissionTo($perm);
            }
        }

        // Ensure manage settings permission belongs to Super Admin by default
        $super->givePermissionTo($manageSettings);
    }
}
