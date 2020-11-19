<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DinhMucSanXuat extends Model
{
    protected $guarded = [];

    public function sanPham(){
        return $this->belongsTo('App\SanPham', 'san_pham_id', 'id');
    }
    public function nguyenLieus(){
        return $this->belongsTo('App\SanPham', 'nguyen_lieu_id', 'id');
    }
    public function nguyenLieuTonKho()
    {
        return $this->belongsTo('App\HangTonKho', 'nguyen_lieu_id', 'san_pham_id');
    }
}
