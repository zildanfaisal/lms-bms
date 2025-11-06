<?php

namespace App\Policies;

use App\Models\LearningLog;
use App\Models\User;

class LearningLogPolicy
{
    public function viewAny(User $user): bool { return $user->can('view any learning log'); }
    public function view(User $user, LearningLog $log): bool { return $user->can('view learning log') || $user->id === optional($log->owner->user)->id; }
    public function create(User $user): bool { return $user->can('create learning log'); }
    public function update(User $user, LearningLog $log): bool { return $user->can('update learning log') || $user->id === optional($log->owner->user)->id && $log->status === LearningLog::STATUS_DRAFT; }
    public function submit(User $user, LearningLog $log): bool { return $user->id === optional($log->owner->user)->id && $log->status === LearningLog::STATUS_DRAFT; }
    public function approve(User $user, LearningLog $log): bool { return $user->can('approve learning log') && $user->id !== optional($log->owner->user)->id && $log->status === LearningLog::STATUS_PENDING; }
    public function reject(User $user, LearningLog $log): bool { return $this->approve($user, $log); }
}
