<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThongBaoTrangThaiThietBi extends Model
{
    protected $fillable = [
        'thiet_bi_id',
        'noi_dung',
        'trang_thai',
        'toa_nha_id',
        'tinh_thanh_id'
    ];
    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh')->select('id', 'name', 'code');
    }
    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha')->select('id', 'ten');
    }
}
