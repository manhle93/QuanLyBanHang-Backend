<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonHangThanhToanNhaCungCap extends Model
{
    protected $guarded = [];
    public function donHang() {
        return $this->belongsTo('App\DonHangNhaCungCap', 'don_hang_id', 'id');
    }
}
