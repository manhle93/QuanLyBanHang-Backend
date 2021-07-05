<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NhapKhoTam extends Model
{
    protected $guarded = [];
    protected $table = 'nhap_kho_tam';

    public function donHang()
    {
        return $this->belongsTo('App\DonHangNhaCungCap', 'don_hang_id');
    }
}
