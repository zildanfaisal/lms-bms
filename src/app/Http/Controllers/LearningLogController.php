<?php

namespace App\Http\Controllers;

use App\Models\LearningLog;
use App\Models\LearningPeriod;
use App\Models\LearningPlatform;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class LearningLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        if (!$karyawan) {
            return view('learning.missing_karyawan');
        }
        $periodOptions = \App\Models\LearningPeriod::orderByDesc('starts_at')->get(['id','name','starts_at','ends_at']);
        $selectedPeriodId = $request->get('period_id') ?: optional($this->currentPeriod())->id;

        $logs = LearningLog::with('platform')
            ->where('karyawan_id', $karyawan->id)
            ->when($selectedPeriodId, fn($q,$pid) => $q->where('period_id',$pid))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('learning.logs.index', [
            'logs' => $logs,
            'periodOptions' => $periodOptions,
            'selectedPeriodId' => $selectedPeriodId,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        if (!$karyawan) {
            return view('learning.missing_karyawan');
        }
        $currentPeriod = $this->currentPeriod();
        $periodOptions = \App\Models\LearningPeriod::orderByDesc('starts_at')->get(['id','name','starts_at','ends_at']);
        $prefill = [
            'platform_id' => $request->query('platform_id'),
            'title' => $request->query('title'),
            'learning_url' => $request->query('learning_url'),
            'evidence_url' => $request->query('evidence_url'),
            'started_at' => $request->query('started_at') ?? now()->toDateString(),
            'ended_at' => $request->query('ended_at') ?? now()->toDateString(),
            'duration_minutes' => $request->query('duration_minutes') ?? 60,
            'recommendation_id' => $request->query('recommendation_id'),
            'period_id' => $request->query('period_id') ?: optional($currentPeriod)->id,
        ];
        return view('learning.logs.create', [
            'prefill' => $prefill,
            'periodOptions' => $periodOptions,
            'platforms' => LearningPlatform::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();
        $period = $this->currentPeriod();

        $data = $request->all();
        Validator::make($data, [
            'period_id' => ['required','exists:learning_periods,id'],
            'platform_id' => ['required','exists:learning_platforms,id'],
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'started_at' => ['required','date'],
            'ended_at' => ['required','date','after_or_equal:started_at'],
            'duration_minutes' => ['required','integer','min:1','max:100000'],
            'learning_url' => ['nullable','url'],
            'evidence_url' => ['nullable','url'],
            'recommendation_id' => ['nullable','integer','exists:learning_recommendations,id'],
        ])->validate();

        $started = Carbon::parse($data['started_at'])->toDateString();
        $ended = Carbon::parse($data['ended_at'])->toDateString();
        $duration = (int) $data['duration_minutes'];

        try {
            $log = LearningLog::create([
                'karyawan_id' => $karyawan->id,
                'platform_id' => $data['platform_id'],
                'period_id' => $data['period_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'started_at' => $started,
                'ended_at' => $ended,
                'duration_minutes' => $duration,
                'learning_url' => $data['learning_url'] ?? null,
                'evidence_url' => $data['evidence_url'] ?? null,
                'recommendation_id' => $data['recommendation_id'] ?? null,
                'status' => LearningLog::STATUS_DRAFT,
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pembelajaran: '.$e->getMessage());
        }

    return redirect()->route('learning.logs.index', ['period_id' => $data['period_id']])->with('status', 'Learning log created as draft.');
    }

    public function update(Request $request, LearningLog $log)
    {
        $this->authorize('update', $log);

        $data = $request->all();
        Validator::make($data, [
            'period_id' => ['required','exists:learning_periods,id'],
            'platform_id' => ['required','exists:learning_platforms,id'],
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'started_at' => ['required','date'],
            'ended_at' => ['required','date','after_or_equal:started_at'],
            'duration_minutes' => ['required','integer','min:1','max:100000'],
            'learning_url' => ['nullable','url'],
            'evidence_url' => ['nullable','url'],
            'recommendation_id' => ['nullable','integer','exists:learning_recommendations,id'],
        ])->validate();

        $started = Carbon::parse($data['started_at'])->toDateString();
        $ended = Carbon::parse($data['ended_at'])->toDateString();
        $duration = (int) $data['duration_minutes'];

        $log->update([
            'platform_id' => $data['platform_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'started_at' => $started,
            'ended_at' => $ended,
            'duration_minutes' => $duration,
            'learning_url' => $data['learning_url'] ?? null,
            'evidence_url' => $data['evidence_url'] ?? null,
            'recommendation_id' => $data['recommendation_id'] ?? null,
        ]);

        return redirect()->back()->with('status', 'Learning log updated.');
    }

    public function submit(Request $request, LearningLog $log)
    {
        $this->authorize('submit', $log);
        $log->update([
            'status' => LearningLog::STATUS_PENDING,
            'submitted_at' => now(),
        ]);
        $log->activities()->create([
            'actor_id' => $request->user()->id,
            'action' => 'submit',
            'meta' => null,
        ]);
        return redirect()->back()->with('status', 'Learning log submitted for approval.');
    }

    public function show(LearningLog $log)
    {
        $this->authorize('view', $log);
        return view('learning.logs.show', ['log' => $log->load('platform','owner.jabatan','activities.actor')]);
    }

    protected function currentPeriod(): ?LearningPeriod
    {
        $today = Carbon::today();
        return LearningPeriod::where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->first();
    }
}
