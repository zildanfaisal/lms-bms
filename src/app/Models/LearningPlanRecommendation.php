<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPlanRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id','title','url','platform_id','description'
    ];

    public function proposal() { return $this->belongsTo(LearningPlanProposal::class, 'proposal_id'); }
    public function platform() { return $this->belongsTo(LearningPlatform::class, 'platform_id'); }
}
