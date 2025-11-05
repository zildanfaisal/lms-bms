<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'unit';

    protected $fillable = [
        'nama_unit',
        'divisi_id',
    ];

    public function divisi()
    { 
        return $this->belongsTo(Divisi::class); 
    }
}
