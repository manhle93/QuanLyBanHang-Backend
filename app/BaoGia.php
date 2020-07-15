<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaoGia extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function sanPhams()
    {
        return $this->hasMany('App\SanPhamBaoGia', 'bao_gia_id', 'id');
    }
}
