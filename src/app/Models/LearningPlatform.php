<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LearningLog;

class LearningPlatform extends Model
{
    use HasFactory;

    protected $table = 'learning_platforms';

    protected $fillable = [
        'name','type','url','description','is_active','created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function logs()
    {
        return $this->hasMany(LearningLog::class, 'platform_id');
    }
}
