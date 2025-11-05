<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Direktorat;
use App\Models\Divisi;
use App\Models\Unit;
use App\Models\Jabatan;
use App\Models\Posisi;
use App\Models\Karyawan;
use App\Models\User;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure at least one Jabatan and Posisi exist
        $jabatanDefault = Jabatan::first();
        if (! $jabatanDefault) {
            $jabatanDefault = Jabatan::create([
                'kode_jabatan' => 'JBT001',
                'nama_jabatan' => 'Staff',
                'level' => 1,
            ]);
        }

        $posisiDefault = Posisi::first();
        if (! $posisiDefault) {
            $posisiDefault = Posisi::create(['nama_posisi' => 'Posisi Umum']);
        }

        // Ensure there is at least one unit (and its parents) — create sample if none
        if (Unit::count() === 0) {
            $direktorat = Direktorat::first() ?? Direktorat::create(['nama_direktorat' => 'Direktorat Umum']);
            $divisi = Divisi::where('direktorat_id', $direktorat->id)->first() ?? Divisi::create(['nama_divisi' => 'Divisi Umum','direktorat_id' => $direktorat->id]);
            Unit::create(['nama_unit' => 'Unit Umum','divisi_id' => $divisi->id]);
        }

        $units = Unit::with('divisi.direktorat')->get();
        $jabatans = Jabatan::all();

        $counter = 1;

        foreach ($units as $unit) {
            // create or reuse a user for this karyawan
            $email = 'karyawan+' . $counter . '@example.com';
            $user = User::where('email', $email)->first();
            if (! $user) {
                if (method_exists(User::class, 'factory')) {
                    $user = User::factory()->create([
                        'name' => 'Karyawan ' . $counter,
                        'email' => $email,
                    ]);
                } else {
                    $user = User::create([
                        'name' => 'Karyawan ' . $counter,
                        'email' => $email,
                        'password' => bcrypt('password'),
                    ]);
                }
            }

            // unique NIK
            $nik = 'KAR'.str_pad($counter, 4, '0', STR_PAD_LEFT);
            while (Karyawan::where('nik', $nik)->exists()) {
                $counter++;
                $nik = 'KAR'.str_pad($counter, 4, '0', STR_PAD_LEFT);
            }

            // assign a jabatan (rotate through existing jabatans)
            $jabatan = $jabatans->count() ? $jabatans->get(($counter - 1) % $jabatans->count()) : $jabatanDefault;

            Karyawan::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik' => $nik,
                    'user_id' => $user->id,
                    'direktorat_id' => $unit->divisi->direktorat->id ?? null,
                    'divisi_id' => $unit->divisi->id ?? null,
                    'unit_id' => $unit->id,
                    'jabatan_id' => $jabatan->id ?? $jabatanDefault->id,
                    'posisi_id' => $posisiDefault->id,
                    'nama' => $user->name,
                    'status_karyawan' => 'Tetap',
                    'no_wa' => null,
                    'tanggal_masuk' => now()->subYear()->toDateString(),
                ]
            );

            $counter++;
        }

        // Ensure every jabatan has at least one karyawan; if not, create one assigned to first unit
        $firstUnit = Unit::first();
        foreach (Jabatan::all() as $jab) {
            $has = Karyawan::where('jabatan_id', $jab->id)->exists();
            if (! $has) {
                $email = 'karyawan-jab-' . $jab->id . '@example.com';
                $user = User::firstOrCreate(
                    ['email' => $email],
                    ['name' => 'Karyawan Jabatan ' . $jab->id, 'password' => bcrypt('password')]
                );

                $nik = 'KAR'.str_pad($counter, 4, '0', STR_PAD_LEFT);
                while (Karyawan::where('nik', $nik)->exists()) {
                    $counter++;
                    $nik = 'KAR'.str_pad($counter, 4, '0', STR_PAD_LEFT);
                }

                Karyawan::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nik' => $nik,
                        'user_id' => $user->id,
                        'direktorat_id' => $firstUnit->divisi->direktorat->id ?? null,
                        'divisi_id' => $firstUnit->divisi->id ?? null,
                        'unit_id' => $firstUnit->id,
                        'jabatan_id' => $jab->id,
                        'posisi_id' => $posisiDefault->id,
                        'nama' => $user->name,
                        'status_karyawan' => 'Tetap',
                        'no_wa' => null,
                        'tanggal_masuk' => now()->subYear()->toDateString(),
                    ]
                );

                $counter++;
            }
        }
    }
}
