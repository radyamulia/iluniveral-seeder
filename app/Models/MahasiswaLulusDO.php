<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MahasiswaLulusDO extends Model
{
    protected $table = 'mahasiswa_lulus_do';
    protected $guarded = [];

    public function mahasiswa() {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }
}
