<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HangTonKho extends Model
{
    protected $guarded = [];
    public function sanPham()
    {
        return $this->belongsTo('App\SanPham', 'san_pham_id');
    }
    public function kho()
    {
        return $this->belongsTo('App\Kho', 'kho_id');
    }
}
