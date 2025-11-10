<?php

namespace App\Http\Controllers;

use App\Models\LearningLog;
use App\Models\Karyawan;
use App\Services\ApproverResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TeamApprovalController extends Controller
{
    public function index(Request $request, ApproverResolver $resolver)
    {
        $user = $request->user();
        $me = Karyawan::where('user_id', $user->id)->with('jabatan')->first();
        if (!$me) {
            // If approver has no karyawan profile, show friendly message instead of 404
            return view('learning.missing_karyawan');
        }
        // Find owners for whom current user is the nearest eligible approver
        $myLevel = optional($me->jabatan)->level ?? PHP_INT_MAX;
        $potentialOwners = Karyawan::with('jabatan')
            ->where('direktorat_id', $me->direktorat_id)
            ->whereHas('jabatan', fn($q) => $q->where('level', '<', $myLevel))
            ->get();

        $eligibleOwnerIds = $potentialOwners
            ->filter(fn($owner) => $resolver->isEligibleApprover($owner, $me))
            ->pluck('id');

        $logs = LearningLog::with(['owner.jabatan','platform','recommendation'])
            ->whereIn('karyawan_id', $eligibleOwnerIds)
            ->where('status', LearningLog::STATUS_PENDING)
            ->orderBy('submitted_at')
            ->paginate(20);

        return view('learning.approvals.index', [
            'logs' => $logs,
        ]);
    }

    public function approve(Request $request, LearningLog $log, ApproverResolver $resolver)
    {
        $this->authorize('approve', $log);

        // Prevent self-approval
        if (optional($log->owner->user)->id === $request->user()->id) {
            abort(403, 'You cannot approve your own learning log.');
        }

        // Ensure current user is an eligible approver for the owner
        $me = Karyawan::where('user_id', $request->user()->id)->with('jabatan')->first();
        if (!$me || !$resolver->isEligibleApprover($log->owner, $me)) {
            abort(403, 'You are not an eligible approver for this learning log.');
        }

        $log->update([
            'status' => LearningLog::STATUS_APPROVED,
            'validated_at' => now(),
            'validated_by' => $request->user()->id,
            'reject_reason' => null,
        ]);
        $log->activities()->create([
            'actor_id' => $request->user()->id,
            'action' => 'approve',
            'meta' => null,
        ]);

        return redirect()->back()->with('status', 'Learning log approved.');
    }

    public function reject(Request $request, LearningLog $log, ApproverResolver $resolver)
    {
        $this->authorize('reject', $log);

        $data = $request->validate([
            'reason' => ['required','string','max:500']
        ]);

        // Prevent self-approval
        if (optional($log->owner->user)->id === $request->user()->id) {
            abort(403, 'You cannot reject your own learning log.');
        }

        // Ensure current user is an eligible approver for the owner
        $me = Karyawan::where('user_id', $request->user()->id)->with('jabatan')->first();
        if (!$me || !$resolver->isEligibleApprover($log->owner, $me)) {
            abort(403, 'You are not an eligible approver for this learning log.');
        }

        $log->update([
            'status' => LearningLog::STATUS_REJECTED,
            'validated_at' => now(),
            'validated_by' => $request->user()->id,
            'reject_reason' => $data['reason'],
        ]);
        $log->activities()->create([
            'actor_id' => $request->user()->id,
            'action' => 'reject',
            'meta' => ['reason' => $data['reason']],
        ]);

        return redirect()->back()->with('status', 'Learning log rejected.');
    }
}
