<?php

namespace App\Http\Controllers;

use App\Models\LearningLog;
use App\Models\LearningPeriod;
use App\Services\TargetResolver;
use Illuminate\Http\Request;

class LearningReportController extends Controller
{
    public function index(Request $request, TargetResolver $resolver)
    {
        $periodId = $request->get('period_id') ?? optional($this->currentPeriod())->id;

        $summary = LearningLog::selectRaw('karyawan_id, SUM(duration_minutes) AS minutes')
            ->where('status', 'approved')
            ->when($periodId, fn($q) => $q->where('period_id', $periodId))
            ->groupBy('karyawan_id')
            ->with(['owner.jabatan','owner.unit','owner.divisi','owner.direktorat'])
            ->paginate(20);

        // Resolve targets for listed karyawan
        $targetsMap = [];
        if ($periodId) {
            $owners = $summary->getCollection()->pluck('owner')->filter();
            $targetsMap = $resolver->forMany($owners, (int)$periodId);
        }

        return view('learning.reports.index', [
            'summary' => $summary,
            'periodId' => $periodId,
            'targetsMap' => $targetsMap,
        ]);
    }

    protected function currentPeriod(): ?LearningPeriod
    {
        return LearningPeriod::whereDate('starts_at', '<=', now())
            ->whereDate('ends_at', '>=', now())
            ->first();
    }
}
