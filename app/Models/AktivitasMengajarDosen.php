<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasMengajarDosen extends Model
{
    protected $table = 'aktivitas_mengajar_dosen';
    protected $guarded = [];

    // API Request methods
    // List Aktivitas Mengajar Dosen
    public function getAktivitasMengajarDosen($token, $filter = '', $order = '', $limit = '', $offset = '')
    {
        return fetchData('GetAktivitasMengajarDosen', $token, $filter, $order, $limit, $offset);
    }

    // Table Relation
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }
}
