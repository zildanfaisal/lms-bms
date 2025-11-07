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
        $periodOptions = \App\Models\LearningPeriod::orderByDesc('starts_at')->get(['id','name']);
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

        // Aggregates (overall completion for current page set)
        $totalWithTarget = 0; $totalPctNumerator = 0; $totalPctDenominator = 0;
        foreach ($summary as $row) {
            $t = $targetsMap[$row->karyawan_id] ?? null;
            if ($t) {
                $totalWithTarget++;
                $totalPctNumerator += min($row->minutes, $t);
                $totalPctDenominator += $t;
            }
        }
        $avgCompletion = ($totalWithTarget && $totalPctDenominator) ? number_format(($totalPctNumerator / max(1,$totalPctDenominator)) * 100, 1) : null;

        return view('learning.reports.index', [
            'summary' => $summary,
            'periodId' => $periodId,
            'targetsMap' => $targetsMap,
            'periodOptions' => $periodOptions,
            'avgCompletion' => $avgCompletion,
        ]);
    }

    /**
     * CSV export of learning progress for a period.
     */
    public function export(Request $request, TargetResolver $resolver)
    {
        $periodId = $request->get('period_id');
        if (!$periodId) {
            return redirect()->route('learning.reports.index')->with('error','Periode wajib dipilih untuk ekspor.');
        }
        $logs = \App\Models\LearningLog::selectRaw('karyawan_id, SUM(duration_minutes) AS minutes')
            ->where('status','approved')
            ->where('period_id', $periodId)
            ->groupBy('karyawan_id')
            ->with(['owner'])
            ->get();
        $targetsMap = $resolver->forMany($logs->pluck('owner')->filter(), (int)$periodId);
        $filename = 'learning_report_period_'.$periodId.'_'.date('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
        $callback = function() use ($logs, $targetsMap) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Karyawan','Minutes','Target','Completion %']);
            foreach ($logs as $row) {
                $name = $row->owner?->nama ?? ('#'.$row->karyawan_id);
                $t = $targetsMap[$row->karyawan_id] ?? null;
                $pct = $t ? number_format(min(100, ($row->minutes / max(1,$t))*100),1) : '';
                fputcsv($out, [$name, $row->minutes, $t ?? '', $pct]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    protected function currentPeriod(): ?LearningPeriod
    {
        return LearningPeriod::whereDate('starts_at', '<=', now())
            ->whereDate('ends_at', '>=', now())
            ->first();
    }
}
