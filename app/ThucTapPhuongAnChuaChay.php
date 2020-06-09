<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThucTapPhuongAnChuaChay extends Model
{
    protected $guarded = [];

    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha');
    }
    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh', 'id', 'tinh_thanh_id');
    }
    public function donVi()
    {
        return $this->hasMany('App\DonViThucTapChuaChay', 'phuong_an_thuc_tap_id', 'id');
    }
    public function quanHuyen()
    {
        return $this->hasMany('App\QuanHuyenThucTapPhuongAnChuaChay', 'phuong_an_thuc_tap_id', 'id');
    }

}
