<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhieuThu extends Model
{
    protected $guarded = [];
    public function khachHang(){
        return $this->belongsTo('App\User', 'user_id_khach_hang');
    }
}
