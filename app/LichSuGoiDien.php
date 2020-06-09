<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LichSuGoiDien extends Model
{
    protected $fillable = [
        'toa_nha_id',
        'so_dien_thoai',
        'diem_chay_id'
    ];
}
