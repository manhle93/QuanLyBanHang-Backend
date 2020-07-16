<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NopTien extends Model
{
    protected $guarded = [];
    public function nguoiTao(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function khachHang(){
        return $this->belongsTo('App\KhachHang', 'id_user_khach_hang', 'user_id');
    }

}
