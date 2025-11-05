<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direktorat extends Model
{
    protected $table = 'direktorat';

    protected $fillable = [
        'nama_direktorat',
    ];

    public function divisis()
    {
        return $this->hasMany(Divisi::class, 'direktorat_id');
    }
}
