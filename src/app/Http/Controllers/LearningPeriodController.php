<?php

namespace App\Http\Controllers;

use App\Models\LearningPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningPeriodController extends Controller
{
    public function index()
    {
        $query = LearningPeriod::query();
        if ($code = request('code')) { $query->where('code','like','%'.$code.'%'); }
        if ($name = request('name')) { $query->where('name','like','%'.$name.'%'); }
        $periods = $query->orderByDesc('starts_at')->paginate(15)->appends(request()->query());
        return view('learning.periods.index', compact('periods'));
    }

    public function create()
    {
        return view('learning.periods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required','string','max:50','unique:learning_periods,code'],
            'name' => ['required','string','max:255'],
            'starts_at' => ['required','date'],
            'ends_at' => ['required','date','after_or_equal:starts_at'],
            'is_locked' => ['nullable','boolean'],
            'is_active' => ['nullable','boolean'],
        ]);

        // Optional overlap prevention: ensure no overlapping active periods
                // Overlap only checked against active periods
                $overlap = LearningPeriod::where('is_active', true)->where(function($q) use ($data){
            $q->whereBetween('starts_at', [$data['starts_at'], $data['ends_at']])
              ->orWhereBetween('ends_at', [$data['starts_at'], $data['ends_at']])
              ->orWhere(function($qq) use ($data){
                  $qq->where('starts_at','<=',$data['starts_at'])
                     ->where('ends_at','>=',$data['ends_at']);
              });
        })->exists();
        if ($overlap) {
            return back()->withInput()->with('error','Periode bertumpuk dengan yang sudah ada.');
        }

        LearningPeriod::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'is_locked' => (bool)($data['is_locked'] ?? false),
            'is_active' => (bool)($data['is_active'] ?? true),
        ]);
        return redirect()->route('learning.periods.index')->with('status','Periode dibuat.');
    }

    public function edit(LearningPeriod $period)
    {
        return view('learning.periods.edit', compact('period'));
    }

    public function update(Request $request, LearningPeriod $period)
    {
        // Allow unlocking when currently locked without requiring other fields
        if ($period->is_locked && !$request->boolean('is_locked')) {
            $period->update(['is_locked' => false]);
            return back()->with('status','Periode telah dibuka (unlocked). Anda sekarang dapat mengubah detailnya.');
        }
        // If still locked and not unlocking, block updates
        if ($period->is_locked) {
            return back()->with('error','Periode terkunci dan tidak dapat diubah.');
        }

        $data = $request->validate([
            'code' => ['required','string','max:50','unique:learning_periods,code,'.$period->id],
            'name' => ['required','string','max:255'],
            'starts_at' => ['required','date'],
            'ends_at' => ['required','date','after_or_equal:starts_at'],
            'is_locked' => ['nullable','boolean'],
            'is_active' => ['nullable','boolean'],
        ]);

        // Optional overlap prevention (excluding current period)
        $overlap = LearningPeriod::where('id','!=',$period->id)
            ->where('is_active', true)
            ->where(function($q) use ($data){
                $q->whereBetween('starts_at', [$data['starts_at'], $data['ends_at']])
                  ->orWhereBetween('ends_at', [$data['starts_at'], $data['ends_at']])
                  ->orWhere(function($qq) use ($data){
                      $qq->where('starts_at','<=',$data['starts_at'])
                         ->where('ends_at','>=',$data['ends_at']);
                  });
            })->exists();
        if ($overlap) {
            return back()->withInput()->with('error','Periode bertumpuk dengan yang sudah ada.');
        }

        $period->update([
            'code' => $data['code'],
            'name' => $data['name'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'is_locked' => (bool)($data['is_locked'] ?? false),
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);
        return redirect()->route('learning.periods.index')->with('status','Periode diperbarui.');
    }

    public function destroy(LearningPeriod $period)
    {
        if ($period->is_locked) {
            return back()->with('error','Periode terkunci dan tidak dapat dihapus.');
        }
        try {
            $period->delete();
        } catch (\Throwable $e) {
            return back()->with('error','Gagal menghapus: '.$e->getMessage());
        }
        return redirect()->route('learning.periods.index')->with('status','Periode dihapus.');
    }
}
