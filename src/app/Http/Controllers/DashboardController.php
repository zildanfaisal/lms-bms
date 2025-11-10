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
        $periodOptions = \App\Models\LearningPeriod::orderByDesc('starts_at')->get(['id','name','starts_at','ends_at']);
        $selectedPeriodId = (int) request()->get('period_id');
        $period = null;
        if ($selectedPeriodId) {
            $period = $periodOptions->firstWhere('id', $selectedPeriodId);
        }
        if (!$period) {
            $period = \App\Models\LearningPeriod::where('starts_at', '<=', $today)
                ->where('ends_at', '>=', $today)
                ->first();
        }

    $target = null;
    $progressMinutes = 0;
    $recommendedItems = [];
    $recommendedItems = [];
    $allPeriodStats = [];
        $platformMap = [];
        if ($karyawan && $period) {
            $resolver = app(\App\Services\TargetResolver::class);
            // Kumpulkan statistik semua periode untuk kartu multi-periode
            foreach ($periodOptions as $p) {
                $pTarget = $resolver->for($karyawan, (int)$p->id);
                $pApproved = \App\Models\LearningLog::where('karyawan_id', $karyawan->id)
                    ->where('period_id', $p->id)
                    ->where('status', 'approved')
                    ->sum('duration_minutes');
                $allPeriodStats[] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'target' => $pTarget,
                    'approved' => $pApproved,
                    'progress_pct' => $pTarget ? min(100, ($pApproved / max(1,$pTarget)) * 100) : null,
                ];
            }
            // Gunakan periode yang dipilih (atau aktif) untuk blok utama
            $target = $resolver->for($karyawan, (int)$period->id);
            $progressMinutes = \App\Models\LearningLog::where('karyawan_id', $karyawan->id)
                ->where('period_id', $period->id)
                ->where('status', 'approved')
                ->sum('duration_minutes');

            // Resolve applied learning recommendations for the user & current period
            $recResolver = app(\App\Services\RecommendationResolver::class);
            $recommendedItems = $recResolver->for($karyawan, (int)$period->id);
            // Otomatis: tandai selesai jika ada log APPROVED terkait rekomendasi pada periode terpilih
            $autoDoneIds = \App\Models\LearningLog::query()
                ->where('karyawan_id', $karyawan->id)
                ->where('period_id', $period->id)
                ->where('status', 'approved')
                ->whereNotNull('recommendation_id')
                ->pluck('recommendation_id')
                ->unique()
                ->all();
            // Tanda 'Menunggu Approve' jika ada log PENDING terkait rekomendasi pada periode terpilih
            $pendingIds = \App\Models\LearningLog::query()
                ->where('karyawan_id', $karyawan->id)
                ->where('period_id', $period->id)
                ->where('status', 'pending')
                ->whereNotNull('recommendation_id')
                ->pluck('recommendation_id')
                ->unique()
                ->all();
            if (!empty($recommendedItems)) {
                $recommendedItems = array_map(function($it) use ($autoDoneIds, $pendingIds){
                    $id = $it['id'] ?? null;
                    if ($id && in_array($id, $autoDoneIds)) {
                        $it['done'] = true;
                        $it['done_source'] = 'auto';
                    } elseif ($id && in_array($id, $pendingIds)) {
                        $it['pending'] = true;
                    }
                    return $it;
                }, $recommendedItems);
            }
            // Map platforms for badges
            $platformIds = collect($recommendedItems)->pluck('platform_id')->filter()->unique()->values();
            if ($platformIds->isNotEmpty()) {
                $platformMap = \App\Models\LearningPlatform::whereIn('id', $platformIds)->pluck('name','id')->toArray();
            }
            // Jika tidak ada rekomendasi untuk user di periode ini, sembunyikan target di dashboard
            if (empty($recommendedItems)) {
                $target = null;
            }
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
            'periodOptions' => $periodOptions,
            'selectedPeriodId' => $period?->id,
            'learningTargetMinutes' => $target,
            'learningApprovedMinutes' => $progressMinutes,
            'allPeriodStats' => $allPeriodStats,
            'recommendedItems' => $recommendedItems,
            'platformMap' => $platformMap,
            'canReviewProposals' => $user?->hasRole('Super Admin') ?? false,
            'recentSubmittedLogs' => $karyawan && $period ? \App\Models\LearningLog::with('platform')
                ->where('karyawan_id',$karyawan->id)
                ->where('period_id',$period->id)
                ->whereIn('status',['pending','approved'])
                ->orderByDesc('submitted_at')
                ->limit(5)
                ->get() : collect(),
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
