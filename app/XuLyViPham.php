<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XuLyViPham extends Model
{
    protected $guarded = [];
    public function files()
    {
        return $this->hasMany('App\File', 'reference_id')->where('type', 'xu_ly_vi_pham');
    }
    public function nhomHanhVis()
    {
        return $this->hasMany('App\ViPhamNhomHanhVi', 'vi_pham_id');
    }
    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha');
    }
}
