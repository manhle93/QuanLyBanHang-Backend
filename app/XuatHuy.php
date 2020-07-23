<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XuatHuy extends Model
{
    protected $guarded = [];
    public function nguoiTao()
    {
        return $this->belongsTo('App\User', 'user_tao_id');
    }
    public function sanPhams()
    {
        return $this->hasMany('App\SanPhamXuatHuy', 'xuat_huy_id');
    }
}
