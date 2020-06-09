<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TinhThanhScope;

class DonViHoTro extends Model
{
    protected $fillable = [
        'ma',
        'ten',
        'loai_don_vi_id',
        'so_dien_thoai',
        'lat',
        'long',
        'tinh_thanh_id',
        'quan_huyen_id'
    ];
    public function loaiDonVi() {
        return $this->belongsTo('App\DanhMuc', 'loai_don_vi_id', 'id');
    }
    public function quanHuyen() {
        return $this->belongsTo('App\QuanHuyen', 'quan_huyen_id', 'id');
    }
    public function tinhThanh() {
        return $this->belongsTo('App\TinhThanh', 'tinh_thanh_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TinhThanhScope);
    }
}
