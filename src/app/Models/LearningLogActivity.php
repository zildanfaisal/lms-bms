<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningLogActivity extends Model
{
    use HasFactory;

    protected $table = 'learning_log_activities';

    protected $fillable = [
        'learning_log_id','actor_id','action','meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function log() { return $this->belongsTo(LearningLog::class, 'learning_log_id'); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
