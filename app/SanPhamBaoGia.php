<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SanPhamBaoGia extends Model
{
    protected $guarded = [];
    public function sanPham()
    {
        return $this->belongsTo('App\SanPham', 'san_pham_id');
    }
    public function baoGia()
    {
        return $this->belongsTo('App\BaoGia', 'bao_gia_id');
    }
}
