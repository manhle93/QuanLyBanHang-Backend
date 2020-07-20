<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KiemKho extends Model
{
    protected $guarded = [];

    public function nguoiTao()
    {
        return $this->belongsTo('App\User', 'user_tao_id');
    }
    public function nhanVien()
    {
        return $this->belongsTo('App\User', 'user_nhan_vien_id');
    }
    public function sanPhams()
    {
        return $this->hasMany('App\SanPhamKiemKho', 'kiem_kho_id');
    }
}
