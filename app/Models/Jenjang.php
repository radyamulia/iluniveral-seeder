<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jenjang extends Model
{
    protected $table = 'jenjang_pendidikan';
    protected $guarded = [];

    // API Request methods
    public static function getAllJenjang($token, $filter = '', $order = '', $limit = '', $offset = '')
    {
        return fetchData('GetJenjangPendidikan', $token, $filter, $order, $limit, $offset);
    }
}
