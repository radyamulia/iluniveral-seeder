<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prodi extends Model
{
    protected $table = 'prodi';
    protected $guarded = [];

    // API Request methods
    public static function getAllProdi($token, $filter = '', $order = '', $limit = '', $offset = '')
    {
        return fetchData('GetProdi', $token, $filter, $order, $limit, $offset);
    }

    public function jenjang_pendidikan(): BelongsTo
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang_didik', 'id_jenjang_didik');
    }
}
