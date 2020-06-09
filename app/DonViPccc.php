<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TinhThanhScope;

class DonViPccc extends Model
{

    protected $guarded = [];

    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh', 'tinh_thanh_id')->select('id', 'name', 'code')->withDefault();
    }
    public function quanHuyen()
    {
        return $this->belongsTo('App\QuanHuyen', 'quan_huyen_id')->select('id', 'name', 'code', 'tinh_thanh_id')->withDefault();
    }
    public function thietBi()
    {
        return $this->hasManyThrough('App\ThietBi', 'App\ToaNha');
    }
    public function phuongTien()
    {
        return $this->hasMany('App\PhuongTienPCCC');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TinhThanhScope);
    }
}
