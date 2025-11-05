<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = 'divisi';

    protected $fillable = [
        'nama_divisi',
        'direktorat_id',
    ];

    public function direktorat()
    { 
        return $this->belongsTo(Direktorat::class); 
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'divisi_id');
    }
}
