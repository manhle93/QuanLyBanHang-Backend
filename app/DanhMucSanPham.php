<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DanhMucSanPham extends Model
{
    protected $guarded = [];
    public function sanPhams()
    {
        return $this->hasMany('App\SanPham', 'danh_muc_id', 'id');
    }
}
