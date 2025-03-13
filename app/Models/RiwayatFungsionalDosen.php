<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatFungsionalDosen extends Model
{
    protected $table = 'riwayat_fungsional_dosen';
    protected $guarded = [];

    // API Request methods
    // List Riwayat Fungsional Dosen
    public function getRiwayatFungsionalDosen($token, $filter = '', $order = '', $limit = '', $offset = '')
    {
        return fetchData('GetRiwayatFungsionalDosen', $token, $filter, $order, $limit, $offset);
    }


    // Table Relation
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }
}
