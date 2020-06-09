<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ToaNhaScope;

class ThietBi extends Model
{
    protected $guarded = [];
    // protected $appends = ['battery'];
    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha');
    }
    public function camBien()
    {
        return $this->hasMany('App\CamBien');
    }
    public function loaiThietBi()
    {
        return $this->belongsTo('App\DanhMuc');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    // public function getBatteryAttribute(){
    //     $json = file_get_contents('http://171.244.49.26:2585/SAS/GetDeviceByUser/123456');
    //     $objs = collect(json_decode($json));
    //     foreach ($objs as $obj) {
    //         if($obj->IMEI ==  $this->attributes['imei']) return $obj->Battery;
    //     }
    //     return null;
    // }
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ToaNhaScope);
        static::creating(function (ThietBi $thietBi) {
            $thietBi->search = convert_vi_to_en($thietBi->ten) . ' ' . convert_vi_to_en($thietBi->column);
        });
        static::updating(function (ThietBi $thietBi) {
            $thietBi->search = convert_vi_to_en($thietBi->ten);
        });
    }
}
