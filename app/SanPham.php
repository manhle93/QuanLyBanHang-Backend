<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $guarded = [];
    public function danhMuc()
    {
        return $this->belongsTo('App\DanhMucSanPham', 'danh_muc_id', 'id');
    }

    public function hinhAnhs()
    {
        return $this->hasMany('App\HinhAnhSanPham', 'san_pham_id', 'id');
    }
}
