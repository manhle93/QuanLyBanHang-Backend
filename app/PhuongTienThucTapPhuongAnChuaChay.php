<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhuongTienThucTapPhuongAnChuaChay extends Model
{
    protected $guarded = [];

    public function phuongTien(){
        return $this->belongsTo('App\PhuongTienPccc', 'phuong_tien_pccc_id' ,'id')->select('id', 'bien_so', 'ten');
    }
    public function phuongAnThucTap(){
        return $this->belongsTo('App\ThucTapPhuongAnChuaChay', 'id', 'phuong_an_thuc_tap_id');
    }
    public function donViThamGia(){
        return $this->belongsTo('App\DonViThucTapChuaChay', 'don_vi_pccc_id', 'don_vi_pccc_tham_gia_id');
    }
    public function quanHuyenThamGia(){
        return $this->belongsTo('App\QuanHuyenThucTapPhuongAnChuaChay', 'quan_huyen_id', 'quan_huyen_tham_gia_id');
    }
    public function donViPccc(){
        return $this->belongsTo('App\DonViPccc', 'don_vi_pccc_tham_gia_id')->select('id', 'ten');
    }
    public function quanHuyen(){
        return $this->belongsTo('App\QuanHuyen', 'quan_huyen_tham_gia_id')->select('id', 'name');
    }
}
