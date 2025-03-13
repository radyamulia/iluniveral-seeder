<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapJumlahMahasiswa extends Model
{
    protected $table = 'rekap_jumlah_mahasiswa';
    protected $guarded = [];

    public function prodi() {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }
}
