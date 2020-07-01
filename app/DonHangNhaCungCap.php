<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonHangNhaCungCap extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    public function sanPhams() {
        return $this->hasMany('App\SanPhamDonHangNhaCungCap', 'don_hang_id', 'id');
    }
    
}
