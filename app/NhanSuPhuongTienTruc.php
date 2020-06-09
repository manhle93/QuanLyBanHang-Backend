<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NhanSuPhuongTienTruc extends Model
{
    protected $guarded = [];

    public function canBoChienSis(){
        return $this->belongsTo('App\CanBoChienSi', 'reference_id', 'id')->select('id', 'ten');
    }
    public function phuongTienPcccs(){
        return $this->belongsTo('App\PhuongTienPCCC', 'reference_id', 'id')->select('id', 'bien_so', 'ten');
    }
}
