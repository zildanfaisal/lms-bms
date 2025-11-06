<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LearningLogActivity;

class LearningLog extends Model
{
    use HasFactory;

    protected $table = 'learning_logs';

    protected $fillable = [
        'karyawan_id','platform_id','period_id','title','description','started_at','ended_at','duration_minutes','evidence_url','evidence_path','status','submitted_at','validated_at','validated_by','reject_reason'
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function owner() { return $this->belongsTo(Karyawan::class, 'karyawan_id'); }
    public function platform() { return $this->belongsTo(LearningPlatform::class, 'platform_id'); }
    public function period() { return $this->belongsTo(LearningPeriod::class, 'period_id'); }
    public function validator() { return $this->belongsTo(User::class, 'validated_by'); }
    public function activities() { return $this->hasMany(LearningLogActivity::class, 'learning_log_id'); }
}
