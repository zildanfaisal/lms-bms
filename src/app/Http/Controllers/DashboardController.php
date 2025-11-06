<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $karyawan = $user?->karyawan;
        $today = \Carbon\Carbon::today();
        $period = \App\Models\LearningPeriod::where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->first();

        $target = null;
        $progressMinutes = 0;
        if ($karyawan && $period) {
            $resolver = app(\App\Services\TargetResolver::class);
            $target = $resolver->for($karyawan, (int)$period->id);
            $progressMinutes = \App\Models\LearningLog::where('karyawan_id', $karyawan->id)
                ->where('period_id', $period->id)
                ->where('status', 'approved')
                ->sum('duration_minutes');
        }

        // Build profile info for the identity card
        $displayName = $karyawan?->nama ?? $user?->name ?? '';
        $displayName = trim((string) $displayName);
        $initials = '';
        if ($displayName !== '') {
            $parts = preg_split('/\s+/', $displayName);
            if ($parts && count($parts) > 0) {
                $initials .= mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1));
                if (count($parts) > 1) {
                    $initials .= mb_strtoupper(mb_substr($parts[1] ?? '', 0, 1));
                }
            }
        }

        return view('dashboard', [
            'learningPeriod' => $period,
            'learningTargetMinutes' => $target,
            'learningApprovedMinutes' => $progressMinutes,
            'profileName' => $displayName ?: '-',
            'profileInitials' => $initials ?: '?',
            'profileDirektorat' => $karyawan?->direktorat?->nama_direktorat ?? '-',
            'profileDivisi' => $karyawan?->divisi?->nama_divisi ?? '-',
            'profileUnit' => $karyawan?->unit?->nama_unit ?? '-',
            'profileJabatan' => $karyawan?->jabatan?->nama_jabatan ?? '-',
            'profilePosisi' => $karyawan?->posisi?->nama_posisi ?? '-',
        ]);
    }
}
