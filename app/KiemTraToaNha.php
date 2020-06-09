<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KiemTraToaNha extends Model
{
    protected $fillable = [
        'can_bo_kiem_tra',
        'quyet_dinh_kiem_tra',
        'thong_tin',
        'danh_gia',
        'ngay_kiem_tra',
        'mo_ta',
        'toa_nha_id',
        'search',
    ];

    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha')->withDefault();
    }

    public function files()
    {
        return $this->hasMany('App\File', 'reference_id')->where('type', 'kiem_tra_toa_nha');
    }
}
