<?php

namespace App\Http\Controllers;

use App\Models\LearningLog;
use App\Models\LearningPeriod;
use App\Models\Direktorat;
use App\Models\Divisi;
use App\Models\Unit;
use App\Services\TargetResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class LearningReportController extends Controller
{
    public function index(Request $request, TargetResolver $resolver)
    {
    $periodOptions = \App\Models\LearningPeriod::where('is_active', true)->orderByDesc('starts_at')->get(['id','name']);
        $periodId = $request->get('period_id') ?? optional($this->currentPeriod())->id;

        // Optional hierarchy filters
        $direktoratId = $request->get('direktorat_id');
        $divisiId = $request->get('divisi_id');
        $unitId = $request->get('unit_id');

        // Role-based restriction: non Super Admin (e.g., Admin) are constrained to their own hierarchy
        $user = Auth::user();
        $isSuperAdmin = $user && DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', \App\Models\User::class)
            ->whereIn('role_id', DB::table('roles')->select('id')->where('name','Super Admin'))
            ->exists();
        $canChooseHierarchy = $isSuperAdmin;
        if ($user && !$canChooseHierarchy) {
            $k = $user->karyawan;
            if ($k) {
                $direktoratId = $k->direktorat_id;
                $divisiId = $k->divisi_id;
                $unitId = $k->unit_id;
            }
        }

        $summary = LearningLog::selectRaw('karyawan_id, SUM(duration_minutes) AS minutes')
            ->where('status', 'approved')
            ->when($periodId, fn($q) => $q->where('period_id', $periodId))
            // apply hierarchy filters via nested whereHas
            ->when($direktoratId, fn($q) => $q->whereHas('owner.direktorat', fn($qq) => $qq->where('id', $direktoratId)))
            ->when($divisiId, fn($q) => $q->whereHas('owner.divisi', fn($qq) => $qq->where('id', $divisiId)))
            ->when($unitId, fn($q) => $q->whereHas('owner.unit', fn($qq) => $qq->where('id', $unitId)))
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

        // Options for selects (cascade style)
        $direktoratOptions = Direktorat::orderBy('nama_direktorat')->get(['id','nama_direktorat']);
        $divisiOptions = Divisi::when($direktoratId, fn($q) => $q->where('direktorat_id', $direktoratId))
            ->orderBy('nama_divisi')->get(['id','nama_divisi']);
        $unitOptions = Unit::when($divisiId, fn($q) => $q->where('divisi_id', $divisiId))
            ->orderBy('nama_unit')->get(['id','nama_unit']);

        return view('learning.reports.index', [
            'summary' => $summary,
            'periodId' => $periodId,
            'targetsMap' => $targetsMap,
            'periodOptions' => $periodOptions,
            'avgCompletion' => $avgCompletion,
            // filter state
            'direktoratId' => $direktoratId,
            'divisiId' => $divisiId,
            'unitId' => $unitId,
            // options
            'direktoratOptions' => $direktoratOptions,
            'divisiOptions' => $divisiOptions,
            'unitOptions' => $unitOptions,
            'canChooseHierarchy' => $canChooseHierarchy,
        ]);
    }

    /**
     * PDF export of learning progress for a period.
     */
    public function exportPdf(Request $request, TargetResolver $resolver)
    {
        $periodId = $request->get('period_id');
        if (!$periodId) {
            return redirect()->route('learning.reports.index')->with('error','Periode wajib dipilih untuk ekspor.');
        }
        $period = LearningPeriod::find($periodId);
        // hierarchy filters
        $direktoratId = $request->get('direktorat_id');
        $divisiId = $request->get('divisi_id');
        $unitId = $request->get('unit_id');

        // Enforce restriction for non Super Admin
    $user = Auth::user();
        $isSuperAdmin = $user && DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', \App\Models\User::class)
            ->whereIn('role_id', DB::table('roles')->select('id')->where('name','Super Admin'))
            ->exists();
        if ($user && !$isSuperAdmin) {
            $k = $user->karyawan;
            if ($k) {
                $direktoratId = $k->direktorat_id;
                $divisiId = $k->divisi_id;
                $unitId = $k->unit_id;
            }
        }

        $rows = \App\Models\LearningLog::selectRaw('karyawan_id, SUM(duration_minutes) AS minutes')
            ->where('status','approved')
            ->where('period_id', $periodId)
            ->when($direktoratId, fn($q) => $q->whereHas('owner.direktorat', fn($qq) => $qq->where('id', $direktoratId)))
            ->when($divisiId, fn($q) => $q->whereHas('owner.divisi', fn($qq) => $qq->where('id', $divisiId)))
            ->when($unitId, fn($q) => $q->whereHas('owner.unit', fn($qq) => $qq->where('id', $unitId)))
            ->groupBy('karyawan_id')
            ->with(['owner.jabatan','owner.unit','owner.divisi','owner.direktorat'])
            ->get();
        $targetsMap = $resolver->forMany($rows->pluck('owner')->filter(), (int)$periodId);
        // Selected models for header card
        $direktorat = $direktoratId ? Direktorat::find($direktoratId) : null;
        $divisi = $divisiId ? Divisi::find($divisiId) : null;
        $unit = $unitId ? Unit::find($unitId) : null;

        // Directly render PDF (inline preview in browser's PDF viewer)
        $html = view('learning.reports.pdf', [
            'period' => $period,
            'rows' => $rows,
            'targetsMap' => $targetsMap,
            'direktorat' => $direktorat,
            'divisi' => $divisi,
            'unit' => $unit,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = 'learning_report_period_'.$periodId.'_'.date('Ymd_His').'.pdf';
        // Inline preview (opens in-tab) while still suggesting filename
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * Show detail per employee for a given period.
     */
    public function show(Request $request, \App\Models\Karyawan $karyawan, TargetResolver $resolver)
    {
        $periodId = $request->get('period_id') ?? optional($this->currentPeriod())->id;
        $logs = \App\Models\LearningLog::with('platform')
            ->where('karyawan_id', $karyawan->id)
            ->when($periodId, fn($q)=>$q->where('period_id', $periodId))
            ->where('status','approved')
            ->orderByDesc('id')
            ->paginate(20);
        $target = $periodId ? $resolver->for($karyawan, (int)$periodId) : null;
        $minutes = $logs->getCollection()->sum('duration_minutes');

        // Optional: resolved recommendations for this user & period
        $recs = [];
        if ($periodId) {
            $recResolver = app(\App\Services\RecommendationResolver::class);
            $recs = $recResolver->for($karyawan, (int)$periodId);
        }

        return view('learning.reports.show', [
            'karyawan' => $karyawan->load(['jabatan','unit','divisi','direktorat']),
            'periodId' => $periodId,
            'logs' => $logs,
            'target' => $target,
            'minutes' => $minutes,
            'recs' => $recs,
        ]);
    }

    protected function currentPeriod(): ?LearningPeriod
    {
        return LearningPeriod::where('is_active', true)
            ->whereDate('starts_at', '<=', now())
            ->whereDate('ends_at', '>=', now())
            ->first();
    }
}
