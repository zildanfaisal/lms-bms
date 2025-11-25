<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Direktorat;
use App\Models\Divisi;
use App\Models\Unit;
use App\Models\Jabatan;
use App\Models\Posisi;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari atau buat Direktorat HR
        $direktorat = Direktorat::where('nama_direktorat', 'LIKE', '%HUMAN RESOURCE%')
            ->orWhere('nama_direktorat', 'LIKE', '%HR%')
            ->first();

        if (!$direktorat) {
            $direktorat = Direktorat::create([
                'nama_direktorat' => 'HUMAN RESOURCES & GENERAL AFFAIR'
            ]);
        }

        // Cari atau buat Divisi untuk HR
        $divisi = Divisi::where('direktorat_id', $direktorat->id)->first();
        if (!$divisi) {
            $divisi = Divisi::create([
                'nama_divisi' => 'HUMAN RESOURCES & GENERAL AFFAIR',
                'direktorat_id' => $direktorat->id
            ]);
        }

        // Cari Unit Human Capital yang sudah ada
        $unit = Unit::where('divisi_id', $divisi->id)
            ->where('nama_unit', 'HUMAN CAPITAL')
            ->first();
        
        if (!$unit) {
            $unit = Unit::create([
                'nama_unit' => 'HUMAN CAPITAL',
                'divisi_id' => $divisi->id
            ]);
        }

        // Cari atau buat Jabatan Manager
        $jabatan = Jabatan::where('kode_jabatan', 'MGR')
            ->orWhere('nama_jabatan', 'LIKE', '%Manager%')
            ->first();

        if (!$jabatan) {
            $jabatan = Jabatan::create([
                'kode_jabatan' => 'MGR',
                'nama_jabatan' => 'Manager',
                'level' => 3
            ]);
        }

        // Cari atau buat Posisi HR Generalist
        $posisi = Posisi::where('nama_posisi', 'HR GENERALIST')->first();
        if (!$posisi) {
            $posisi = Posisi::create([
                'nama_posisi' => 'HR GENERALIST'
            ]);
        }

        // Buat User Super Admin
        $user = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password')
            ]
        );

        // Buat Karyawan untuk Super Admin
        $karyawan = Karyawan::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nik' => 'SA001',
                'user_id' => $user->id,
                'direktorat_id' => $direktorat->id,
                'divisi_id' => $divisi->id,
                'unit_id' => $unit->id,
                'jabatan_id' => $jabatan->id,
                'posisi_id' => $posisi->id,
                'nama' => 'Super Admin',
                'status_karyawan' => 'Tetap',
                'no_wa' => null,
                'tanggal_masuk' => now()->subYears(2)->toDateString(),
            ]
        );

        // Assign role Super Admin
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $user->syncRoles([$role->name]);

        $this->command->info('Super Admin user created successfully!');
        $this->command->info('Email: superadmin@example.com');
        $this->command->info('Password: password');
    }
}
