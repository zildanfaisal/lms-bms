<?php

namespace App\Http\Controllers;

use App\Models\LearningPlanProposal;
use App\Models\LearningPlanRecommendation;
use App\Models\LearningRecommendation;
use App\Models\User;
use App\Models\LearningPeriod;
use App\Models\Direktorat;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningPlanProposalController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $query = LearningPlanProposal::with(['period'])
            ->where('proposer_id', $user->id);

        // Filters
        $status = request('status');
        $periodId = request('period_id');
        $scopeType = request('scope_type');
        if ($status) { $query->where('status', $status); }
        if ($periodId) { $query->where('period_id', $periodId); }
        if ($scopeType) { $query->where('scope_type', $scopeType); }

        $proposals = $query->orderByDesc('id')->paginate(10)->appends(request()->query());

        // Resolve scope names (avoid N+1 for mixed types)
        $scopeNames = [];
        $byType = [
            'direktorat' => [],
            'divisi' => [],
            'unit' => [],
        ];
        foreach ($proposals as $p) {
            $byType[$p->scope_type][] = $p->scope_id;
        }
        if (!empty($byType['direktorat'])) {
            $dirs = \App\Models\Direktorat::whereIn('id', array_unique($byType['direktorat']))->get(['id','nama_direktorat']);
            foreach ($dirs as $d) { $scopeNames['direktorat-'.$d->id] = $d->nama_direktorat; }
        }
        if (!empty($byType['divisi'])) {
            $divs = \App\Models\Divisi::whereIn('id', array_unique($byType['divisi']))->get(['id','nama_divisi']);
            foreach ($divs as $d) { $scopeNames['divisi-'.$d->id] = $d->nama_divisi; }
        }
        if (!empty($byType['unit'])) {
            $units = \App\Models\Unit::whereIn('id', array_unique($byType['unit']))->get(['id','nama_unit']);
            foreach ($units as $u) { $scopeNames['unit-'.$u->id] = $u->nama_unit; }
        }

    $periodOptions = \App\Models\LearningPeriod::where('is_active', true)->orderByDesc('starts_at')->get(['id','name']);
        $allowedScopeTypes = $user?->hasRole('Super Admin') ? ['direktorat','divisi','unit'] : ['unit'];

        return view('learning.plans.index', [
            'proposals' => $proposals,
            'scopeNames' => $scopeNames,
            'periodOptions' => $periodOptions,
            'allowedScopeTypes' => $allowedScopeTypes,
            'activeFilters' => [
                'status' => $status,
                'period_id' => $periodId,
                'scope_type' => $scopeType,
            ],
        ]);
    }

    public function create()
    {
    $periods = LearningPeriod::where('is_active', true)->orderByDesc('starts_at')->get();
        $direktorats = Direktorat::orderBy('nama_direktorat')->get();
        $user = request()->user();
        $divisiId = $user?->karyawan?->divisi_id;
        $units = $divisiId ? Unit::where('divisi_id', $divisiId)->orderBy('nama_unit')->get() : collect();
        // For Admin (manager), restrict to unit-level proposals; Super Admin can choose others later
        $allowedScopeTypes = $user?->hasRole('Super Admin') ? ['direktorat','divisi','unit'] : ['unit'];
        return view('learning.plans.create', compact('periods','direktorats','units','allowedScopeTypes','divisiId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'period_id' => ['required', \Illuminate\Validation\Rule::exists('learning_periods','id')->where('is_active', true)],
            'scope_type' => ['required','in:direktorat,divisi,unit'],
            'scope_id' => ['required','integer'],
            'only_subordinate_jabatans' => ['nullable','boolean'],
            'notes' => ['nullable','string'],
            'recs' => ['array'],
            'recs.*.title' => ['required','string','max:255'],
            'recs.*.url' => ['nullable','url'],
            'recs.*.target_minutes' => ['nullable','integer','min:1'],
        ]);
        // Validate scope_id existence based on scope_type
        $scopeType = $data['scope_type'];
        if ($scopeType === 'direktorat') {
            $request->validate(['scope_id' => ['exists:direktorat,id']]);
        } elseif ($scopeType === 'divisi') {
            $request->validate(['scope_id' => ['exists:divisi,id']]);
        } elseif ($scopeType === 'unit') {
            $request->validate(['scope_id' => ['exists:unit,id']]);
        }

        // Auto-calculate total target minutes from recommendations
        $totalTarget = collect($data['recs'] ?? [])->sum(function($r){ return (int)($r['target_minutes'] ?? 0); });
        $proposal = LearningPlanProposal::create([
            'proposer_id' => $request->user()->id,
            'period_id' => $data['period_id'],
            'scope_type' => $data['scope_type'],
            'scope_id' => $data['scope_id'],
            'target_minutes' => $totalTarget > 0 ? $totalTarget : null,
            'only_subordinate_jabatans' => (bool)($data['only_subordinate_jabatans'] ?? false),
            'notes' => $data['notes'] ?? null,
            'status' => 'draft',
        ]);

        foreach (($data['recs'] ?? []) as $rec) {
            LearningPlanRecommendation::create([
                'proposal_id' => $proposal->id,
                'title' => $rec['title'],
                'url' => $rec['url'] ?? null,
                'target_minutes' => $rec['target_minutes'] ?? null,
            ]);
        }

    return redirect()->route('learning.plans.index')->with('success','Draft berhasil dibuat.');
    }

    public function edit(LearningPlanProposal $proposal)
    {
        $this->authorizeOwner($proposal);
    $periods = LearningPeriod::where('is_active', true)->orderByDesc('starts_at')->get();
        $direktorats = Direktorat::orderBy('nama_direktorat')->get();
        $proposal->load('recommendations');
        $user = request()->user();
        $allowedScopeTypes = $user?->hasRole('Super Admin') ? ['direktorat','divisi','unit'] : ['unit'];

        // Preload dependent select data for current scope (for Super Admin UX)
        $divisisForDirektorat = collect();
        $unitsForDivisi = collect();
        $initDirektoratId = null; $initDivisiId = null; $initUnitId = null;
        if ($proposal->scope_type === 'divisi') {
            $divisi = \App\Models\Divisi::find($proposal->scope_id);
            if ($divisi) {
                $divisisForDirektorat = \App\Models\Divisi::where('direktorat_id', $divisi->direktorat_id)->orderBy('nama_divisi')->get(['id','nama_divisi','direktorat_id']);
                $initDirektoratId = $divisi->direktorat_id; $initDivisiId = $divisi->id;
            }
        } elseif ($proposal->scope_type === 'unit') {
            $unit = \App\Models\Unit::with('divisi.direktorat')->find($proposal->scope_id);
            if ($unit && $unit->divisi) {
                $divisisForDirektorat = \App\Models\Divisi::where('direktorat_id', $unit->divisi->direktorat_id)->orderBy('nama_divisi')->get(['id','nama_divisi','direktorat_id']);
                $unitsForDivisi = \App\Models\Unit::where('divisi_id', $unit->divisi_id)->orderBy('nama_unit')->get(['id','nama_unit','divisi_id']);
                $initDirektoratId = $unit->divisi->direktorat_id; $initDivisiId = $unit->divisi_id; $initUnitId = $unit->id;
            }
        } elseif ($proposal->scope_type === 'direktorat') {
            $initDirektoratId = $proposal->scope_id;
        }

        return view('learning.plans.edit', compact('proposal','periods','direktorats','allowedScopeTypes','divisisForDirektorat','unitsForDivisi','initDirektoratId','initDivisiId','initUnitId'));
    }

    public function update(Request $request, LearningPlanProposal $proposal)
    {
        $this->authorizeOwner($proposal);
        if ($proposal->status !== 'draft') {
            return back()->with('error','Hanya draft yang bisa diubah.');
        }
        $data = $request->validate([
            'period_id' => ['required', \Illuminate\Validation\Rule::exists('learning_periods','id')->where('is_active', true)],
            'scope_type' => ['required','in:direktorat,divisi,unit'],
            'scope_id' => ['required','integer'],
            'only_subordinate_jabatans' => ['nullable','boolean'],
            'notes' => ['nullable','string'],
            'recs' => ['array'],
            'recs.*.id' => ['nullable','integer'],
            'recs.*.title' => ['required','string','max:255'],
            'recs.*.url' => ['nullable','url'],
            'recs.*.target_minutes' => ['nullable','integer','min:1'],
        ]);
        // Validate scope_id existence based on scope_type
        $scopeType = $data['scope_type'];
        if ($scopeType === 'direktorat') {
            $request->validate(['scope_id' => ['exists:direktorat,id']]);
        } elseif ($scopeType === 'divisi') {
            $request->validate(['scope_id' => ['exists:divisi,id']]);
        } elseif ($scopeType === 'unit') {
            $request->validate(['scope_id' => ['exists:unit,id']]);
        }

        $totalTarget = collect($data['recs'] ?? [])->sum(function($r){ return (int)($r['target_minutes'] ?? 0); });
        $proposal->update([
            'period_id' => $data['period_id'],
            'scope_type' => $data['scope_type'],
            'scope_id' => $data['scope_id'],
            'target_minutes' => $totalTarget > 0 ? $totalTarget : null,
            'only_subordinate_jabatans' => (bool)($data['only_subordinate_jabatans'] ?? false),
            'notes' => $data['notes'] ?? null,
        ]);

        // sync recs (simple: delete then recreate)
        $proposal->recommendations()->delete();
        foreach (($data['recs'] ?? []) as $rec) {
            LearningPlanRecommendation::create([
                'proposal_id' => $proposal->id,
                'title' => $rec['title'],
                'url' => $rec['url'] ?? null,
                'target_minutes' => $rec['target_minutes'] ?? null,
            ]);
        }

    return redirect()->route('learning.plans.index')->with('success','Perubahan usulan disimpan.');
    }

    public function submit(LearningPlanProposal $proposal)
    {
        $this->authorizeOwner($proposal);
        if ($proposal->status !== 'draft') {
            return back()->with('error','Hanya draft yang bisa di-submit.');
        }
        $proposal->status = 'submitted';
        $proposal->save();
    return redirect()->route('learning.plans.index')->with('success','Usulan berhasil dikirim ke HR.');
    }

    protected function authorizeOwner(LearningPlanProposal $proposal): void
    {
        abort_unless($proposal->proposer_id === request()->user()->id, 403);
    }

    /**
     * AJAX impact preview: returns counts of jabatan & karyawan that would be impacted.
     * Query params: scope_type, scope_id, only_subordinate_jabatans (0/1)
     */
    public function impactPreview(Request $request)
    {
        $data = $request->validate([
            'scope_type' => ['required','in:direktorat,divisi,unit'],
            'scope_id' => ['required','integer'],
            'only_subordinate_jabatans' => ['nullable','boolean'],
        ]);
        $scopeType = $data['scope_type'];
        $scopeId = (int)$data['scope_id'];
        $onlySub = (bool)($data['only_subordinate_jabatans'] ?? false);

        // Validate scope existence quickly
        if ($scopeType === 'direktorat' && !\App\Models\Direktorat::where('id',$scopeId)->exists()) {
            return response()->json(['error' => 'Direktorat tidak ditemukan'], 404);
        }
        if ($scopeType === 'divisi' && !\App\Models\Divisi::where('id',$scopeId)->exists()) {
            return response()->json(['error' => 'Divisi tidak ditemukan'], 404);
        }
        if ($scopeType === 'unit' && !\App\Models\Unit::where('id',$scopeId)->exists()) {
            return response()->json(['error' => 'Unit tidak ditemukan'], 404);
        }

        $proposer = $request->user();
        $managerLevel = optional($proposer->karyawan?->jabatan)->level;
        $subordinateJabatanIds = collect();
        if ($onlySub && $scopeType === 'unit') {
            $subordinateJabatanIds = \App\Models\Jabatan::query()
                ->when(isset($managerLevel), fn($q) => $q->where('level','<', $managerLevel))
                ->pluck('id');
        }

        // Build karyawan base query by scope
        $karyawanQuery = \App\Models\Karyawan::query();
        if ($scopeType === 'unit') {
            $karyawanQuery->where('unit_id', $scopeId);
        } elseif ($scopeType === 'divisi') {
            $unitIds = \App\Models\Unit::where('divisi_id', $scopeId)->pluck('id');
            $karyawanQuery->whereIn('unit_id', $unitIds);
        } elseif ($scopeType === 'direktorat') {
            $divisiIds = \App\Models\Divisi::where('direktorat_id', $scopeId)->pluck('id');
            $unitIds = \App\Models\Unit::whereIn('divisi_id', $divisiIds)->pluck('id');
            $karyawanQuery->whereIn('unit_id', $unitIds);
        }
        if ($onlySub && $scopeType === 'unit' && $subordinateJabatanIds->isNotEmpty()) {
            $karyawanQuery->whereIn('jabatan_id', $subordinateJabatanIds);
        }

        $totalKaryawans = $karyawanQuery->count();
        $jabatanCounts = $karyawanQuery->select('jabatan_id')
            ->whereNotNull('jabatan_id')
            ->groupBy('jabatan_id')
            ->selectRaw('jabatan_id, COUNT(*) as c')
            ->get();
        $jabatanIds = $jabatanCounts->pluck('jabatan_id')->filter();
        $jabatans = \App\Models\Jabatan::whereIn('id', $jabatanIds)->get(['id','nama_jabatan','level'])->keyBy('id');
        $jabatanRows = $jabatanCounts->map(function($row) use ($jabatans) {
            $j = $jabatans[$row->jabatan_id] ?? null;
            return [
                'id' => $row->jabatan_id,
                'nama_jabatan' => $j?->nama_jabatan ?? ('#'.$row->jabatan_id),
                'level' => $j?->level,
                'karyawan_count' => (int)$row->c,
            ];
        })->values();

        return response()->json([
            'scope_type' => $scopeType,
            'scope_id' => $scopeId,
            'only_subordinate_jabatans' => $onlySub,
            'total_jabatans' => $jabatanRows->count(),
            'total_karyawans' => $totalKaryawans,
            'jabatans' => $jabatanRows,
        ]);
    }

    /**
     * History detail of a proposal for modal popup (JSON).
     */
    public function history(LearningPlanProposal $proposal)
    {
        // Eager load minimal relations
        $proposal->load(['period','proposer','recommendations']);

        // Resolve scope name
        $scopeName = match($proposal->scope_type) {
            'unit' => optional(\App\Models\Unit::find($proposal->scope_id))->nama_unit,
            'divisi' => optional(\App\Models\Divisi::find($proposal->scope_id))->nama_divisi,
            'direktorat' => optional(\App\Models\Direktorat::find($proposal->scope_id))->nama_direktorat,
            default => null,
        };

        // Applied recommendations count (when approved)
        $appliedCount = LearningRecommendation::where('approved_proposal_id', $proposal->id)->count();

        $approverName = $proposal->approved_by ? optional(User::find($proposal->approved_by))->name : null;

        return response()->json([
            'id' => $proposal->id,
            'period' => $proposal->period?->name,
            'scope_type' => $proposal->scope_type,
            'scope_id' => $proposal->scope_id,
            'scope_name' => $scopeName,
            'only_subordinate_jabatans' => (bool)$proposal->only_subordinate_jabatans,
            'target_minutes' => $proposal->target_minutes,
            'status' => $proposal->status,
            'approved_at' => optional($proposal->approved_at)?->toDateTimeString(),
            'approved_by' => $approverName,
            'rejected_reason' => $proposal->rejected_reason,
            'created_at' => optional($proposal->created_at)?->toDateTimeString(),
            'proposer' => [
                'id' => $proposal->proposer?->id,
                'name' => $proposal->proposer?->name,
                'email' => $proposal->proposer?->email,
            ],
            'recommendations' => $proposal->recommendations->map(function($r){
                return [
                    'title' => $r->title,
                    'url' => $r->url,
                    'target_minutes' => $r->target_minutes,
                ];
            })->values(),
            'applied_recommendations' => $appliedCount,
        ]);
    }
}
