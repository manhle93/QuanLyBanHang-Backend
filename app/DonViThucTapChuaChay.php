<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonViThucTapChuaChay extends Model
{
    protected $guarded = [];
    protected $appends = ['phuong_tien_tham_gia', 'nhan_su_tham_gia', 'ten_don_vi'];
    public function donVi(){
        return $this->belongsTo('App\DonViPccc', 'don_vi_pccc_id', 'id');
    }
    public function phuongAnThucTap(){
        return $this->belongsTo('App\ThucTapPhuongAnChuaChay', 'id', 'phuong_an_thuc_tap_id');
    }

    public function getPhuongTienThamGiaAttribute(){
        return PhuongTienThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $this->attributes['phuong_an_thuc_tap_id'])->where('don_vi_pccc_tham_gia_id', $this->attributes['don_vi_pccc_id'])->select('id', 'phuong_tien_pccc_id')->get();
    }

    public function getNhanSuThamGiaAttribute(){
        return NhanSuThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $this->attributes['phuong_an_thuc_tap_id'])->where('don_vi_pccc_tham_gia_id', $this->attributes['don_vi_pccc_id'])->select('id', 'can_bo_chien_si_id')->get();
    }
    public function getTenDonViAttribute(){
        return DonViPccc::where('id', $this->attributes['don_vi_pccc_id'])->first()->ten;
    }
}
