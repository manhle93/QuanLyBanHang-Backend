<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BangGia extends Model
{
    protected $guarded = [];
    public function sanPham(){
          return $this->hasMany('App\BangGiaSanPham', 'bang_gia_id');
    }
}
