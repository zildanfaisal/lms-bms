<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Direktorat;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map Direktorat => [Divisi...]
        $map = [
            'CEO' => [],

            'SALES & MARKETING' => [
                'COMMERCIAL PARTNERSHIP',
                'ENTERPRISE',
                'PARTNERSHIP MARKETING & B2C',
                'PAYMENT AGENT',
                'PAYMENT SWITCHING H2H',
                'PAYMENT SWITCHING GPPG',
                'TECHNOLOGY DRIVER BUSINESS',
                'PERFORMANCE & BUSINESS ANALYST',
            ],

            'FINANCE & ACCOUNTING' => [
                'FINANCE',
                'ACCOUNTING',
            ],

            'OPERATION & IT' => [
                'COMPLIANCE & RISK MANAGEMENT',
                'IT SBF SOLUTION',
                'INFRASTRUCTURE & DEVOPS',
                'E-PAYMENT SOLUTION',
                'IT PAYMENT GATEWAY',
                'OPERATION & SERVICE',
            ],

            'OPERATION & SERVICE Lama' => [
                'B2C OPERATION & SERVICE',
                'B2B OPERATION & SERVICE',
                'LOGISTIC',
                'OPERATION & SERVICE',
                'OPERATION SERVER',
                'RECONSILIATION & SETTLEMENT',
            ],

            'HUMAN RESOURCES & GENERAL AFFAIR' => [
                'HUMAN RESOURCES & GENERAL AFFAIR',
            ],

            'RIDE HAILING' => [
                'OPERATIONAL ACI',
                'PARTNERSHIP MARKETING & REGULATORY',
                'DOMPET DIGITAL SPEEDCASH',
                'CORPORATE SOCIAL RESPONSIBILITY',
                'JOGJAKITA',
                'MARKETING ACI',
            ],

            'COMPLIANCE & RISK MANAGEMENT' => [
                'INTERNAL AUDIT & QUALITY ASSURANCE',
                'IT GOVERNANCE & SECURITY COMPLIANCE',
            ],

            'PAYMENT GATEWAY WINPAY' => [
                'OPERATION SERVICE',
                'IT WINPAY',
                'WINPAY SOHO',
                'ENTERPRISE PARTNER REGIONAL TIMUR',
                'ENTERPRISE PARTNER REGIONAL BARAT',
                'LEGAL',
                'PARTNERSHIP MARKETING & REGULATORY',
            ],

            'B2B & DOMPET DIGITAL' => [
                'REGIONAL PARTNER',
                'BANK & NATIONAL PARTNER',
                'PAYMENT SWITCHING H2H',
                'PAYMENT AGENT',
                'PAYMENT SWITCHING GPPG',
                'PERFORMANCE & BUSINESS ANALYST',
                'EKSPEDISI FASTPAY',
                'DOMPET DIGITAL SPEEDCASH',
            ],

            'HUMAN RESOURCE & GENERAL AFFAIR' => [
                'GENERAL AFFAIR',
            ],

            'COMMERCIAL PARTNERSHIP' => [],
        ];

        foreach ($map as $direktoratName => $divisis) {
            $direktorat = Direktorat::where('nama_direktorat', $direktoratName)->first();
            if (!$direktorat) {
                // Skip if parent doesn't exist; expected to be seeded by DirektoratSeeder
                continue;
            }

            foreach ($divisis as $divisiName) {
                $exists = Divisi::where('direktorat_id', $direktorat->id)
                    ->where('nama_divisi', $divisiName)
                    ->first();
                if (!$exists) {
                    $div = new Divisi();
                    $div->direktorat_id = $direktorat->id;
                    $div->nama_divisi = $divisiName;
                    $div->save();
                }
            }
        }
    }
}
