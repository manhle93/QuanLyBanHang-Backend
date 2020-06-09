<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CamBien extends Model
{
    protected $fillable = [
        'ma', 'mo_ta', 'loai_cam_bien_id', 'thiet_bi_id', 'vi_tri', 'so_lan', 'trang_thai_id', 'IMEI_thiet_bi', 'ngay_trien_khai', 'ngay_het_han',
    ];
    // protected $appends = ['battery'];
    protected $guarded = [];

    public function loaiCamBien()
    {
        return $this->belongsTo('App\DanhMuc', 'loai_cam_bien_id');
    }

    public function trangThai()
    {
        return $this->belongsTo('App\DanhMuc', 'trang_thai_id');
    }

    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha');
    }

    public function thietBi()
    {
        return $this->belongsTo('App\ThietBi');
    }
    // public function getBatteryAttribute()
    // {
    //     $json = file_get_contents('http://171.244.49.26:2585/SAS/GetDeviceByUser/123456');
    //     $objs = collect(json_decode($json));
    //     foreach ($objs as $obj) {
    //         if ($obj->Sensors) {
    //             foreach ($obj->Sensors as $cb) {
    //                 if ($cb->IMEI == $this->attributes['IMEI_thiet_bi']) return $cb->Battery;
    //             }
    //         }
    //     }
    //     return null;
    // }

    public function scopeKhuVuc($query)
    {
        if (auth()->user()->role->code == "admin")
            return $query;
        else {
            return $query->whereIn('thiet_bi_id', ThietBi::select('id')->get()->pluck('id')->all());
        }
    }
}
