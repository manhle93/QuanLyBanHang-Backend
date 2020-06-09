<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TinhThanhScope;
class ThietBiQuay extends Model
{
    protected $table = 'thiet_bi_quays';
    protected $fillable = [
        'ma',
        'ten',
        'loai_thiet_bi_quay_id',
        'loai_may_quay_id',
        'tinh_thanh_id',
        'ngay_trien_khai',
        'trang_thai',
        'mo_ta',
        'long',
        'lat',
        'toa_nha_id',
        'camera_id',
        'username',
        'password',
        'ip',
        'port',
        'link',
    ];

    protected $appends = ['status_name'];

    public function loaiThietBi()
    {
        return $this->belongsTo('App\DanhMuc','loai_thiet_bi_quay_id');
    }
    public function loaiMayQuay()
    {
        return $this->belongsTo('App\DanhMuc','loai_may_quay_id');
    }

    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh')->select('id','name','code');
    }

    public function getStatusNameAttribute()
    {
        if(isset($this->attributes['trang_thai'])){
            if ($this->attributes['trang_thai']=="dang_hoat_dong") {
                return $this->attributes['status_name']="Đang hoạt động";
            }
        } 
        else{
            return $this->attributes['status_name']="";
        }
    }
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TinhThanhScope());
    }
}
