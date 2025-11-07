<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id','scope_type','scope_id','jabatan_id','title','url','platform_id','approved_proposal_id','created_by'
    ];

    public function period() { return $this->belongsTo(LearningPeriod::class, 'period_id'); }
    public function platform() { return $this->belongsTo(LearningPlatform::class, 'platform_id'); }
    public function jabatan() { return $this->belongsTo(Jabatan::class, 'jabatan_id'); }
}
