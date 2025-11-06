<?php

namespace App\Http\Controllers;

use App\Models\LearningTarget;
use App\Models\LearningPeriod;
use App\Models\Karyawan;
use App\Models\Direktorat;
use App\Models\Divisi;
use Illuminate\Http\Request;

class LearningTargetController extends Controller
{
    public function index(Request $request)
    {
        $periodId = $request->get('period_id') ?? optional($this->currentPeriod())->id;
        $targets = LearningTarget::with(['karyawan','period'])
            ->when($periodId, fn($q) => $q->where('period_id',$periodId))
            ->orderByDesc('id')
            ->paginate(20);
        $periods = LearningPeriod::orderByDesc('starts_at')->get();
        $direktorats = Direktorat::orderBy('nama_direktorat')->get();
        $divisis = collect();
        return view('learning.targets.index', compact('targets','periodId','periods','direktorats','divisis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'period_id' => ['required','exists:learning_periods,id'],
            'karyawan_id' => ['nullable','exists:karyawan,id'],
            'jabatan_id' => ['nullable','exists:jabatan,id'],
            'unit_id' => ['nullable','exists:unit,id'],
            'divisi_id' => ['nullable','exists:divisi,id'],
            'direktorat_id' => ['nullable','exists:direktorat,id'],
            'target_minutes' => ['required','integer','min:0'],
        ]);

        // ensure at least one scope is provided
        if (!($data['karyawan_id'] ?? null) && !($data['jabatan_id'] ?? null) && !($data['unit_id'] ?? null) && !($data['divisi_id'] ?? null) && !($data['direktorat_id'] ?? null)) {
            return back()->withErrors(['scope' => 'Please provide at least one target scope.']);
        }

        // Save a single target record for the chosen scope.
        // Effective target per-karyawan will be resolved at runtime (karyawan>jabatan>unit>divisi>direktorat)
        LearningTarget::create($data);
        return back()->with('status', 'Target saved for selected scope. All karyawan in that scope will inherit this target for the selected period.');
    }

    public function update(Request $request, LearningTarget $target)
    {
        $data = $request->validate([
            'target_minutes' => ['required','integer','min:0'],
        ]);
        $target->update($data);
        return back()->with('status', 'Target updated');
    }

    public function destroy(LearningTarget $target)
    {
        $target->delete();
        return back()->with('status', 'Target deleted');
    }

    protected function currentPeriod(): ?LearningPeriod
    {
        return LearningPeriod::whereDate('starts_at', '<=', now())
            ->whereDate('ends_at', '>=', now())
            ->first();
    }
}
