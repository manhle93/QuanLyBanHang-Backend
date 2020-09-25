<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonDatHang extends Model
{
    protected $guarded = [];
    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    public function sanPhams()
    {
        return $this->hasMany('App\SanPhamDonDatHang', 'don_dat_hang_id', 'id');
    }

    public function traHang()
    {
        return $this->hasMany('App\DoiTraHang', 'don_hang_id', 'id');
    }

    public function thanhToanBoXung()
    {
        return $this->hasMany('App\ThanhToanBoXung', 'don_hang_id', 'id');
    }
}
