<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Direktorat;
use App\Models\Divisi;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map [Direktorat => [Divisi => [Units...]]]
        $map = [
            'SALES & MARKETING' => [
                'COMMERCIAL PARTNERSHIP' => [
                    'PARTNERSHIP STRATEGIC',
                    'PARTNERSHIP LOCAL PRODUCT',
                ],
                'ENTERPRISE' => [
                    'DIGITAL MARKETING & BRAND ACTIVATION',
                    'MERCHANT ACQUISITION SIDOARJO',
                    'MERCHANT ACQUISITION JAKARTA',
                ],
                'PARTNERSHIP MARKETING & B2C' => [
                    'DOMPET DIGITAL SPEEDCASH',
                    'ENABLING PARTNERSHIP & REGULATORY RELATION',
                    'MODERN RETAIL & PARTNERSHIP MARKETING',
                ],
                'PAYMENT AGENT' => [
                    'AGENT ACQUISITION MGM',
                    'AGENT ACQUISITION SALES FORCE',
                    'BRAND ACTIVATION & DIGITAL MARKETING',
                    'GROWTH AGENT & REVENUE',
                    'COLLECTIVE MARKETING',
                    'SALES FORCE',
                ],
                'PAYMENT SWITCHING H2H' => [
                    'PAYMENT SWITCHING H2H',
                ],
                'PAYMENT SWITCHING GPPG' => [
                    'GPPG CUSTOMER SERVICE',
                    'GPPG OPERATION',
                    'GPPG DIGITAL MARKETING',
                ],
                'TECHNOLOGY DRIVER BUSINESS' => [
                    'TECHNOLOGY DRIVER BUSINESS',
                    'DIGITAL MARKETING & BRAND ACTIVATION',
                ],
                'PERFORMANCE & BUSINESS ANALYST' => [
                    'PERFORMANCE & BUSINESS ANALYST',
                ],
            ],

            'FINANCE & ACCOUNTING' => [
                'FINANCE' => [
                    'ACCOUNTING & TAX',
                    'FINANCE',
                ],
                'ACCOUNTING' => [
                    'ACCOUNTING & TAX',
                ],
            ],

            'OPERATION & IT' => [
                'COMPLIANCE & RISK MANAGEMENT' => [
                    'IT GOVERNANCE & SECURITY COMPLIANCE',
                    'LEGAL & BUSINESS SUSTAINABILITY',
                    'INTERNAL AUDIT & QUALITY ASSURANCE',
                ],
                'IT SBF SOLUTION' => [
                    'IT H2H',
                    'CORE & BILLER',
                    'CHANEL SERVICE',
                    'DATABASE ADMINISTRATION',
                    'PRODUCT DEVELOPMENT & IT SUPPORT',
                ],
                'INFRASTRUCTURE & DEVOPS' => [
                    'DEVOPS',
                    'NETWORK INFRASTRUCTURE',
                ],
                'E-PAYMENT SOLUTION' => [
                    'E-PAYMENT SOLUTION',
                ],
                'IT PAYMENT GATEWAY' => [
                    'IT PAYMENT GATEWAY',
                ],
                'OPERATION & SERVICE' => [
                    'OPERATION & CUSTOMER SERVICE',
                    'RECONSILIATION & SETTLEMENT',
                    'LOGISTIC',
                    'CUSTOMER SERVICE',
                    'OPERATION SERVER',
                    'OPERATION & SERVICE',
                ],
            ],

            'OPERATION & SERVICE Lama' => [
                'B2C OPERATION & SERVICE' => [
                    'OPERATION & CUSTOMER SERVICE',
                ],
                'B2B OPERATION & SERVICE' => [
                    'CUSTOMER SERVICE',
                    'LOGISTIC',
                    'OPERATION & SERVICE',
                    'OPERATION SERVER',
                    'RECONSILIATION & SETTLEMENT',
                ],
                'LOGISTIC' => [
                    'LOGISTIC',
                ],
                'OPERATION & SERVICE' => [
                    'OPERATION & SERVICE',
                ],
                'OPERATION SERVER' => [
                    'OPERATION SERVER',
                ],
                'RECONSILIATION & SETTLEMENT' => [
                    'RECONSILIATION & SETTLEMENT',
                ],
            ],

            'HUMAN RESOURCES & GENERAL AFFAIR' => [
                'HUMAN RESOURCES & GENERAL AFFAIR' => [
                    'HUMAN RESOURCES & GENERAL AFFAIR JOGJA',
                    'TALENT ACQUISITION & BUSINESS PARTNER',
                    'DEVELOPMENT',
                    'GENERAL AFFAIR',
                    'CORPORATE SOCIAL RESPONSIBILITY',
                    'HUMAN CAPITAL',
                    'SECURITY',
                ],
            ],

            'RIDE HAILING' => [
                'OPERATIONAL ACI' => [
                    'PRODUCT & PLATFORM IT SUPPORT',
                    'PRODUCT & PLATFORM IT ECOBIZ',
                    'PRODUCT & PLATFORM IT FRONTEND',
                    'PRODUCT & PLATFORM IT BACKEND',
                    'ECOBIZ DRIVER ENGAGEMENT',
                    'MARKETING',
                    'ECOBIZ OPERATIONAL',
                    'OPERATIONAL ACI',
                    'QUALITY ASSURANCE',
                ],
                'PARTNERSHIP MARKETING & REGULATORY' => [
                    'ENABLING PARTNERSHIP & REGULATORY',
                    'MODERN RETAIL & PARTNERSHIP MARKETING',
                ],
                'DOMPET DIGITAL SPEEDCASH' => [
                    'DOMPET DIGITAL SPEEDCASH',
                ],
                'CORPORATE SOCIAL RESPONSIBILITY' => [
                    'CORPORATE SOCIAL RESPONSIBILITY',
                ],
                'JOGJAKITA' => [
                    'DRIVER OPERATION',
                    'MARKETING ONLINE',
                    'GENERAL AFFAIR',
                ],
                // 'MARKETING ACI' has no units listed
            ],

            'COMPLIANCE & RISK MANAGEMENT' => [
                'INTERNAL AUDIT & QUALITY ASSURANCE' => [
                    'INTERNAL AUDIT & QUALITY ASSURANCE',
                ],
                'IT GOVERNANCE & SECURITY COMPLIANCE' => [
                    'IT GOVERNANCE & SECURITY COMPLIANCE',
                ],
            ],

            'PAYMENT GATEWAY WINPAY' => [
                'OPERATION SERVICE' => [
                    'OPERATION SERVICE',
                ],
                'IT WINPAY' => [
                    'IT WINPAY ENTERPRISE',
                    'IT WINPAY SOHO',
                ],
                'WINPAY SOHO' => [
                    'MERCHANT ACQUISITION SEGMENT',
                ],
                'ENTERPRISE PARTNER REGIONAL TIMUR' => [
                    'PARTNER REGIONAL TIMUR',
                ],
                'ENTERPRISE PARTNER REGIONAL BARAT' => [
                    'MERCHANT ACQUISITION BARAT',
                ],
                'LEGAL' => [
                    'LEGAL PAYMENT GATEWAY',
                ],
                // 'PARTNERSHIP MARKETING & REGULATORY' has no units listed
            ],

            'B2B & DOMPET DIGITAL' => [
                'REGIONAL PARTNER' => [
                    'REGIONAL PARTNER',
                ],
                'BANK & NATIONAL PARTNER' => [
                    'BANK & NATIONAL PARTNER',
                ],
                'PAYMENT SWITCHING H2H' => [
                    'PAYMENT SWITCHING H2H',
                ],
                'PAYMENT AGENT' => [
                    'AGENT ACQUISITION MGM',
                    'GROWTH AGENT & REVENUE',
                    'TERRITORY MARKETING',
                    'BRAND ACTIVATION & DIGITAL MARKETING',
                    'AGENT ACQUISITION SALES FORCE',
                    'SALES FORCE',
                    'MARKETING EKSPEDISI',
                ],
                'PAYMENT SWITCHING GPPG' => [
                    'GPPG OPERATION',
                    'GPPG CUSTOMER SERVICE',
                    'GPPG DIGITAL MARKETING',
                ],
                'PERFORMANCE & BUSINESS ANALYST' => [
                    'PERFORMANCE & BUSINESS ANALYST',
                ],
                // 'EKSPEDISI FASTPAY' has no units listed
                // 'DOMPET DIGITAL SPEEDCASH' has no units listed
            ],

            'HUMAN RESOURCE & GENERAL AFFAIR' => [
                'GENERAL AFFAIR' => [
                    'GENERAL AFFAIR',
                ],
            ],
        ];

        foreach ($map as $direktoratName => $divisiUnits) {
            $direktorat = Direktorat::where('nama_direktorat', $direktoratName)->first();
            if (!$direktorat) {
                // Expect Direktorat seeded already
                continue;
            }

            foreach ($divisiUnits as $divisiName => $units) {
                $divisi = Divisi::where('direktorat_id', $direktorat->id)
                    ->where('nama_divisi', $divisiName)
                    ->first();
                if (!$divisi) {
                    // Divisi might not exist if DivisiSeeder not run; skip safely
                    $divisi = new Divisi();
                    $divisi->direktorat_id = $direktorat->id;
                    $divisi->nama_divisi = $divisiName;
                    $divisi->save();
                }

                foreach ($units as $unitName) {
                    $exists = Unit::where('divisi_id', $divisi->id)
                        ->where('nama_unit', $unitName)
                        ->first();
                    if (!$exists) {
                        $unit = new Unit();
                        $unit->divisi_id = $divisi->id;
                        $unit->nama_unit = $unitName;
                        $unit->save();
                    }
                }
            }
        }
    }
}
