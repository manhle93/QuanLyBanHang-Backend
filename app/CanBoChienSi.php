<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanBoChienSi extends Model
{
    protected $fillable = [
        'ten',
        'so_dien_thoai',
        'ngay_sinh',
        'don_vi_pccc_id',
        'tinh_thanh_id',
        'quan_huyen_id',
        'loai_nhan_su',
        'cap_bac_id',
        'chuc_vu_id',
        'cmnd'
    ];

    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh');
    }
    public function capBac()
    {
        return $this->belongsTo('App\CapBac');
    }
    public function chucVu()
    {
        return $this->belongsTo('App\ChucVu');
    }
    public function quanHuyen()
    {
        return $this->belongsTo('App\QuanHuyen');
    }

    public function donViPccc()
    {
        return $this->belongsTo('App\DonViPccc');
    }
}
