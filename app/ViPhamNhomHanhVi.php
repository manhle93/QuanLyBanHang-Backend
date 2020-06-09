<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViPhamNhomHanhVi extends Model
{
    protected $guarded = [];
    protected $appends = ['ten_nhom_hanh_vi'];
    public function viPham()
    {
        return $this->belongsTo('App\NhomHanhViViPham', 'vi_pham_id');
    }
    public function nhomHanhVi()
    {
        return $this->belongsTo('App\NhomHanhViViPham', 'nhom_hanh_vi_id');
    }
    public function getTenNhomHanhViAttribute(){
        return NhomHanhViViPham::where('id', $this->attributes['nhom_hanh_vi_id'])->first()->noi_dung;
    }
}
