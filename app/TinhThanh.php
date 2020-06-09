<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TinhThanh extends Model
{
    protected $table = 'tinh_thanhs';
    protected $fillable = [
        'code',
        'name',
    ];
    public function user()
    {
        return $this->belongsTo('App\User', 'tinh_thanh_id');
    }
    public function quanHuyen()
    {
        return $this->hasMany('App\QuanHuyen')->select('id', 'name', 'code', 'tinh_thanh_id');
    }
    public function phuongTienPccc()
    {
        return $this->hasManyThrough('App\PhuongTienPccc', 'App\DonViPccc');
    }
    public function donViHoTros()
    {
        return $this->hasMany('App\DonViHoTro', 'tinh_thanh_id', 'id');
    }
    public function thietBi()
    {
        return $this->hasManyThrough('App\ThietBi', 'App\ToaNha');
    }
    public function toaNha()
    {
        return $this->hasMany('App\ToaNha');
    }
    public function donViPccc()
    {
        return $this->hasMany('App\DonViPccc');
    }
}
