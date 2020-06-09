<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HuanLuyenBoiDuong extends Model
{
    protected $guarded = [];
    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha', 'toa_nha_id', 'id');
    }
}
