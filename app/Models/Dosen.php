<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen';
    protected $guarded = [];

    // API Request methods
    public function getAllDosen($token, $filter = '', $order = '', $limit = '', $offset = '')
    {
        return fetchData('GetListDosen', $token, $filter, $order, $limit, $offset);
    }
}
