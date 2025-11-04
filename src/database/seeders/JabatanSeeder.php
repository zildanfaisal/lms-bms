<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        // kode_jabatan + nama_jabatan + level
        $items = [
            ['STF', 'Spesialis', 1],
            ['SPV', 'Supervisor', 2],
            ['MGR', 'Manager', 3],
            ['SM',  'Senior Manager', 4],
            ['EVP', 'Executive Vice President', 5],
            ['CHF',  'Chief', 6],
            ['CEO', 'CEO', 7],
        ];

        foreach ($items as [$kode, $nama, $level]) {
            Jabatan::updateOrCreate(
                ['kode_jabatan' => $kode],
                ['nama_jabatan' => $nama, 'level' => $level]
            );
        }
    }
}
