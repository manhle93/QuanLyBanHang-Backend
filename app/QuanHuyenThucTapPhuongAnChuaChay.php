<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuanHuyenThucTapPhuongAnChuaChay extends Model
{
    protected $guarded = [];
    protected $appends = ['phuong_tien_tham_gia', 'nhan_su_tham_gia', 'ten_quan_huyen'];
    public function quanHuyen(){
        return $this->belongsTo('App\QuanHuyen', 'quan_huyen_id', 'id');
    }

    public function phuongAnThucTap(){
        return $this->belongsTo('App\ThucTapPhuongAnChuaChay', 'id', 'phuong_an_thuc_tap_id');
    }
    // public function phuongTien()
    // {
    //     return $this->hasMany('App\PhuongTienThucTapPhuongAnChuaChay', 'quan_huyen_tham_gia_id', 'quan_huyen_id');
    // }
    // public function canBoChienSi()
    // {
    //     return $this->hasMany('App\NhanSuThucTapPhuongAnChuaChay', 'quan_huyen_tham_gia_id', 'quan_huyen_id');
    // }
    public function getPhuongTienThamGiaAttribute(){
        return PhuongTienThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $this->attributes['phuong_an_thuc_tap_id'])->where('quan_huyen_tham_gia_id', $this->attributes['quan_huyen_id'])->select('id', 'phuong_tien_pccc_id')->get();
    }

    public function getNhanSuThamGiaAttribute(){
        return NhanSuThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $this->attributes['phuong_an_thuc_tap_id'])->where('quan_huyen_tham_gia_id', $this->attributes['quan_huyen_id'])->select('id', 'can_bo_chien_si_id')->get();
    }
    public function getTenQuanHuyenAttribute(){
        return QuanHuyen::where('id', $this->attributes['quan_huyen_id'])->first()->name;
    }
}
