<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuanHuyen extends Model
{
    protected $table = 'quan_huyens';
    protected $fillable = [
        'code',
        'name',
        'tinh_thanh_id'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'quan_huyen_id');
    }
    public function tinhThanh() {
        return $this->belongsTo('App\TinhThanh', 'tinh_thanh_id')->select('id','name','code');
    }
    public function donViHoTros()
    {
        return $this->hasMany('App\DonViHoTro', 'quan_huyen_id', 'id');
    }
}
