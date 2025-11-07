<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPlanProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposer_id','period_id','scope_type','scope_id','target_minutes','only_subordinate_jabatans','notes','status','approved_by','approved_at','rejected_reason'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'only_subordinate_jabatans' => 'boolean',
    ];

    public function proposer() { return $this->belongsTo(User::class, 'proposer_id'); }
    public function period() { return $this->belongsTo(LearningPeriod::class, 'period_id'); }
    public function recommendations() { return $this->hasMany(LearningPlanRecommendation::class, 'proposal_id'); }
}
