<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SanPhamDonDatHang extends Model
{
    protected $guarded = [];
    public function sanPham()
    {
        return $this->belongsTo('App\SanPham', 'san_pham_id');
    }
}
