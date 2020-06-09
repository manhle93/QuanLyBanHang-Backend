<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhuongTienPccc extends Model
{
    protected $guarded = [];
    public function loaiPhuongTienPccc()
    {
        return $this->belongsTo('App\DanhMuc', 'loai_phuong_tien_pccc_id');
    }
    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh', 'tinh_thanh_id', 'id');
    }
    public function quanHuyen()
    {
        return $this->belongsTo('App\QuanHuyen', 'quan_huyen_id', 'id')->select('id', 'code', 'name');
    }
    public function donViPccc()
    {
        return $this->belongsTo('App\DonViPccc');
    }

}
