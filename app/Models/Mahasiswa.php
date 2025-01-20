<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $guarded = [];

    // API Request methods
    public function getAllMhs($token, $filter = '', $order = '', $limit = '', $offset = '') {
        return fetchData('GetListMahasiswa', $token, $filter, $order, $limit, $offset);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }
}
