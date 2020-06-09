<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuuHoCuuNan extends Model
{
    protected $guarded = [];
    
    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh', 'tinh_thanh_id')->select('id', 'name', 'code')->withDefault();
    }

    public function quanHuyen()
    {
        return $this->belongsTo('App\QuanHuyen', 'quan_huyen_id')->select('id', 'name', 'code', 'tinh_thanh_id')->withDefault();
    }
}
