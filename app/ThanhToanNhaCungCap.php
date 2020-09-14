<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThanhToanNhaCungCap extends Model
{
    protected $guarded = [];
    public function donHangs() {
        return $this->hasMany('App\DonHangThanhToanNhaCungCap', 'don_thanh_toan_id', 'id');
    }
    public function nhanCungCap() {
        return $this->belongsTo('App\NhaCungCap', 'nha_cung_cap_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
