<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;

class SanPham extends Model
{
    protected $guarded = [];
    public function danhMuc()
    {
        return $this->belongsTo('App\DanhMucSanPham', 'danh_muc_id', 'id');
    }
    public function nguyenLieus()
    {
        return $this->hasMany('App\DinhMucSanXuat', 'san_pham_id', 'id');
    }
    public function hinhAnhs()
    {
        return $this->hasMany('App\HinhAnhSanPham', 'san_pham_id', 'id');
    }
    public function bangGias()
    {
        return $this->belongsToMany('App\BangGia', 'bang_gia_san_phams');
    }
    public function thuongHieu()
    {
        return $this->belongsTo('App\ThuongHieu', 'thuong_hieu_id', 'id');
    }
    public function sanPhamTonKho()
    {
        return $this->hasOne('App\HangTonKho', 'san_pham_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ActiveScope);
    }
}
