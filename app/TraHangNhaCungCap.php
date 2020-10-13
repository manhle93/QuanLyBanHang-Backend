<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraHangNhaCungCap extends Model
{
    protected $guarded = [];
    public function sanPhams()
    {
        return $this->hasMany('App\SanPhamTraNhaCungCap', 'don_tra_hang_id');
    }
    public function nhaCungCap(){
        return $this->belongsTo('App\NhaCungCap', 'nha_cung_cap_id', 'id');
    }
}
