<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningTarget extends Model
{
    use HasFactory;

    protected $table = 'learning_targets';

    protected $fillable = [
        'period_id', 'karyawan_id', 'jabatan_id', 'unit_id', 'divisi_id', 'direktorat_id', 'target_minutes',
    ];

    public function period() { return $this->belongsTo(LearningPeriod::class, 'period_id'); }
    public function karyawan() { return $this->belongsTo(Karyawan::class, 'karyawan_id'); }
    public function jabatan() { return $this->belongsTo(Jabatan::class, 'jabatan_id'); }
    public function unit() { return $this->belongsTo(Unit::class, 'unit_id'); }
    public function divisi() { return $this->belongsTo(Divisi::class, 'divisi_id'); }
    public function direktorat() { return $this->belongsTo(Direktorat::class, 'direktorat_id'); }
}
