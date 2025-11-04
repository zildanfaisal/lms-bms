<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $fillable = [
        'nik','user_id','direktorat_id','divisi_id','unit_id','jabatan_id','posisi_id','nama','status_karyawan','no_wa','tanggal_masuk','atasan_karyawan_id',
    ];

    public function user()
    { 
        return $this->belongsTo(User::class); 
    }

    public function direktorat()
    { 
        return $this->belongsTo(Direktorat::class); 
    }

    public function divisi()
    { 
        return $this->belongsTo(Divisi::class); 
    }

    public function unit()
    { 
        return $this->belongsTo(Unit::class); 
    }

    public function jabatan()
    { 
        return $this->belongsTo(Jabatan::class); 
    }

    public function posisi()
    { 
        return $this->belongsTo(Posisi::class); 
    }
}
