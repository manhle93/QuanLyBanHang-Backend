<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $guarded = [];
    public function sanPhams()
    {
        return $this->hasMany('App\SanPhamVoucher', 'voucher_id');
    }
}
