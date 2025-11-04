<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Direktorat;
use App\Models\Divisi;
use App\Models\Unit;

class DirektoratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed only top-level Direktorat names
        $direktorats = [
            'CEO',
            'SALES & MARKETING',
            'FINANCE & ACCOUNTING',
            'OPERATION & IT',
            'OPERATION & SERVICE Lama',
            'HUMAN RESOURCES & GENERAL AFFAIR',
            'RIDE HAILING',
            'COMPLIANCE & RISK MANAGEMENT',
            'PAYMENT GATEWAY WINPAY',
            'B2B & DOMPET DIGITAL',
            'HUMAN RESOURCE & GENERAL AFFAIR',
            'COMMERCIAL PARTNERSHIP',
        ];

        foreach ($direktorats as $name) {
            $existing = Direktorat::where('nama_direktorat', $name)->first();
            if (!$existing) {
                $d = new Direktorat();
                $d->nama_direktorat = $name;
                $d->save();
            }
        }
    }
}
