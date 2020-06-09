<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoDienThoaiToaNha extends Model
{
    protected $fillable = [
        'so_dien_thoai',
        'toa_nha_id',
    ];
    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha', 'toa_nha_id', 'id');
    }

}
