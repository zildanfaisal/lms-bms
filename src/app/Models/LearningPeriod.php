<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LearningTarget;

class LearningPeriod extends Model
{
    use HasFactory;

    protected $table = 'learning_periods';

    protected $fillable = [
        'code','name','starts_at','ends_at','is_locked',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_locked' => 'boolean',
    ];

    public function targets()
    {
        return $this->hasMany(LearningTarget::class, 'period_id');
    }
}
