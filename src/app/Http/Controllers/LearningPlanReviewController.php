<?php

namespace App\Http\Controllers;

use App\Models\LearningPlanProposal;
use App\Models\Direktorat;
use App\Models\Divisi;
use App\Models\Unit;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Services\PlanApprovalService;
use Illuminate\Http\Request;

class LearningPlanReviewController extends Controller
{
    public function index()
    {
        // HR/Super Admin queue
        $proposals = LearningPlanProposal::with(['period','proposer.karyawan.jabatan'])
            ->where('status','submitted')
            ->orderBy('id')
            ->paginate(10);

        // Resolve scope names and impact summary per proposal
        $scopeNames = [];
        $impact = [];
        $byType = [ 'direktorat' => [], 'divisi' => [], 'unit' => [] ];
        foreach ($proposals as $p) { $byType[$p->scope_type][] = $p->scope_id; }
        if ($byType['direktorat']) {
            $dirs = Direktorat::whereIn('id', array_unique($byType['direktorat']))->get(['id','nama_direktorat']);
            foreach ($dirs as $d) { $scopeNames['direktorat-'.$d->id] = $d->nama_direktorat; }
        }
        if ($byType['divisi']) {
            $divs = Divisi::whereIn('id', array_unique($byType['divisi']))->get(['id','nama_divisi']);
            foreach ($divs as $d) { $scopeNames['divisi-'.$d->id] = $d->nama_divisi; }
        }
        if ($byType['unit']) {
            $units = Unit::whereIn('id', array_unique($byType['unit']))->get(['id','nama_unit']);
            foreach ($units as $u) { $scopeNames['unit-'.$u->id] = $u->nama_unit; }
        }

        foreach ($proposals as $p) {
            $jabatanCount = 0; $karyawanCount = 0;
            if ($p->scope_type === 'unit') {
                if ($p->only_subordinate_jabatans) {
                    $mgrLevel = optional($p->proposer->karyawan->jabatan)->level;
                    $subJabatanIds = $mgrLevel !== null
                        ? Jabatan::where('level','<',$mgrLevel)->pluck('id')
                        : collect([]);
                    $jabatanCount = $subJabatanIds->count();
                    if ($jabatanCount) {
                        $karyawanCount = Karyawan::where('unit_id', $p->scope_id)
                            ->whereIn('jabatan_id', $subJabatanIds)
                            ->count();
                    }
                } else {
                    $jabatanCount = Karyawan::where('unit_id', $p->scope_id)->distinct('jabatan_id')->count('jabatan_id');
                    $karyawanCount = Karyawan::where('unit_id', $p->scope_id)->count();
                }
            } elseif ($p->scope_type === 'divisi') {
                $jabatanCount = Karyawan::where('divisi_id', $p->scope_id)->distinct('jabatan_id')->count('jabatan_id');
                $karyawanCount = Karyawan::where('divisi_id', $p->scope_id)->count();
            } elseif ($p->scope_type === 'direktorat') {
                $jabatanCount = Karyawan::where('direktorat_id', $p->scope_id)->distinct('jabatan_id')->count('jabatan_id');
                $karyawanCount = Karyawan::where('direktorat_id', $p->scope_id)->count();
            }
            $impact[$p->id] = [ 'jabatan' => $jabatanCount, 'karyawan' => $karyawanCount ];
        }

        return view('learning.reviews.index', compact('proposals','scopeNames','impact'));
    }

    public function show(LearningPlanProposal $proposal)
    {
        $proposal->load(['period','proposer.karyawan.jabatan','recommendations']);

        // Impact detail for show page
        $jabatanRows = [];
        if ($proposal->scope_type === 'unit' && $proposal->only_subordinate_jabatans) {
            $mgrLevel = optional($proposal->proposer->karyawan->jabatan)->level;
            $subJabatan = $mgrLevel !== null ? Jabatan::where('level','<',$mgrLevel)->get(['id','nama_jabatan']) : collect();
            foreach ($subJabatan as $j) {
                $count = Karyawan::where('unit_id', $proposal->scope_id)->where('jabatan_id',$j->id)->count();
                $jabatanRows[] = ['name' => $j->nama_jabatan, 'count' => $count];
            }
        }

        // Resolve scope name
        $scopeName = match($proposal->scope_type) {
            'unit' => optional(Unit::find($proposal->scope_id))->nama_unit,
            'divisi' => optional(Divisi::find($proposal->scope_id))->nama_divisi,
            'direktorat' => optional(Direktorat::find($proposal->scope_id))->nama_direktorat,
            default => null,
        };

        return view('learning.reviews.show', [
            'proposal' => $proposal,
            'scopeName' => $scopeName,
            'jabatanRows' => $jabatanRows,
        ]);
    }

    public function approve(LearningPlanProposal $proposal, PlanApprovalService $service)
    {
        if ($proposal->status !== 'submitted') {
            return back()->with('error','Status usulan tidak valid untuk approve.');
        }
        $service->apply($proposal, request()->user()->id);
        return redirect()->route('learning.reviews.index')->with('status','Usulan telah disetujui dan diterapkan.');
    }

    public function reject(Request $request, LearningPlanProposal $proposal)
    {
        if ($proposal->status !== 'submitted') {
            return back()->with('error','Status usulan tidak valid untuk reject.');
        }
        $data = $request->validate(['reason' => ['required','string','max:1000']]);
        $proposal->status = 'rejected';
        $proposal->rejected_reason = $data['reason'];
        $proposal->approved_by = request()->user()->id;
        $proposal->approved_at = now();
        $proposal->save();
        return redirect()->route('learning.reviews.index')->with('status','Usulan ditolak.');
    }

    /**
     * Super Admin history index: list all approved or rejected proposals with filters and pagination.
     */
    public function historyIndex(Request $request)
    {
        $query = LearningPlanProposal::with(['period','proposer.karyawan.jabatan'])
            ->whereIn('status',['approved','rejected']);

        $periodId = $request->get('period_id');
        $status = $request->get('status'); // optional: approved/rejected
        $scopeType = $request->get('scope_type');
        if ($periodId) { $query->where('period_id',$periodId); }
        if ($status && in_array($status,['approved','rejected'])) { $query->where('status',$status); }
        if ($scopeType && in_array($scopeType,['direktorat','divisi','unit'])) { $query->where('scope_type',$scopeType); }

        $proposals = $query->orderByDesc('approved_at')->paginate(15)->appends($request->query());

        // Resolve scope names (batched)
        $scopeNames = [];
        $byType = ['direktorat'=>[],'divisi'=>[],'unit'=>[]];
        foreach ($proposals as $p) { $byType[$p->scope_type][] = $p->scope_id; }
        if ($byType['direktorat']) {
            $dirs = Direktorat::whereIn('id', array_unique($byType['direktorat']))->get(['id','nama_direktorat']);
            foreach ($dirs as $d) { $scopeNames['direktorat-'.$d->id] = $d->nama_direktorat; }
        }
        if ($byType['divisi']) {
            $divs = Divisi::whereIn('id', array_unique($byType['divisi']))->get(['id','nama_divisi']);
            foreach ($divs as $d) { $scopeNames['divisi-'.$d->id] = $d->nama_divisi; }
        }
        if ($byType['unit']) {
            $units = Unit::whereIn('id', array_unique($byType['unit']))->get(['id','nama_unit']);
            foreach ($units as $u) { $scopeNames['unit-'.$u->id] = $u->nama_unit; }
        }

        $periodOptions = \App\Models\LearningPeriod::orderByDesc('starts_at')->get(['id','name']);

        return view('learning.reviews.history', [
            'proposals' => $proposals,
            'scopeNames' => $scopeNames,
            'periodOptions' => $periodOptions,
            'activeFilters' => [
                'period_id' => $periodId,
                'status' => $status,
                'scope_type' => $scopeType,
            ],
        ]);
    }
}
