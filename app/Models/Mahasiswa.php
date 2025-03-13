<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $guarded = [];

    // API Request methods
    // List Mahasiswa Data
    public function getAllMhs($token, $filter = '', $order = '', $limit = '', $offset = '') {
        return fetchData('GetListMahasiswa', $token, $filter, $order, $limit, $offset);
    }

    // Rekap Jumlah Mahasiswa Aktif
    public function getRekapJumlahMhs($token, $filter = '', $order = '', $limit = '', $offset = '') {
        return fetchData('GetRekapJumlahMahasiswa', $token, $filter, $order, $limit, $offset);
    }

    // Rekap Jumlah Mahasiswa Lulus
    public function getAllMhsLulusDO($token, $filter = '', $order = '', $limit = '', $offset = '') {
        // dd($filter);
        return fetchData('GetListMahasiswaLulusDO', $token, $filter, $order, $limit, $offset);
    }


    // Table Relation 
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }
}
