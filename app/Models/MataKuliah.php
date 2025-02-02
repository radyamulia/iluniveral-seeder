<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MataKuliah extends Model
{
    protected $table = 'matakuliah';
    protected $guarded = [];

    protected $attributes = [
        'sks_mata_kuliah' => 0.00,
        'sks_tatap_muka' => 0.00,
        'sks_praktek' => 0.00,
        'sks_praktek_lapangan' => 0.00,
        'sks_simulasi' => 0.00,
    ];

    // API Request methods
    public function getAllMataKuliah($token, $filter = '', $order = '', $limit = '', $offset = '')
    {
        return fetchData('GetListMataKuliah', $token, $filter, $order, $limit, $offset);
    }

    public function jenjang_pendidikan(): BelongsTo
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang_didik', 'id_jenjang_didik');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }
}
