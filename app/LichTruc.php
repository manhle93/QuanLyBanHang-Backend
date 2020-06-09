<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LichTruc extends Model
{
    protected $guarded = [];
    public function tinhThanh(){
        return $this->belongsTo('App\TinhThanh', 'tinh_thanh_id')->select('id', 'name');
    }
    public function donViPccc(){
        return $this->belongsTo('App\DonViPccc', 'don_vi_id')->select('id', 'ten');
    }
    public function nhanSus(){
        return $this->hasMany('App\NhanSuPhuongTienTruc', 'truc_id', 'id')->where('type', 'cbcs')->select('truc_id','reference_id', 'type', 'reference_id', 'chi_tiet_nhan_su');
    }
    public function phuongTiens(){
        return $this->hasMany('App\NhanSuPhuongTienTruc', 'truc_id', 'id')->where('type', 'phuong_tien')->select('truc_id','reference_id', 'type', 'reference_id', 'chi_tiet_nhan_su');
    }
}
