<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TinhThanhScope;

class DiemLayNuoc extends Model
{
    protected $fillable = [
        'ma',
        'ten',
        'dia_chi',
        'tinh_thanh_id',
        'quan_huyen_id',
        'lat',
        'long',
        'search',
        'description',
        'status',
        'important',
        'don_vi_quan_ly',
        'loai',
        'don_vi_quan_ly_id',
        'kha_nang_cap_nuoc_cho_xe',
        'hien_thi_tren_map'
    ];


    public function quanHuyen()
    {
        return $this->belongsTo('App\QuanHuyen', 'quan_huyen_id', 'id')->select('id', 'name', 'code', 'tinh_thanh_id');
    }

    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh', 'tinh_thanh_id', 'id')->select('id', 'name', 'code');
    }
    public function getDonViQuanLyAttribute(){
        if($this->attributes['don_vi_quan_ly_id'] && $this->attributes['don_vi_quan_ly'] == null){
            return DonViPccc::where('id', $this->attributes['don_vi_quan_ly_id'])->first()->ten;
        }else{
            return $this->attributes['don_vi_quan_ly'];
        }
        
    }
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TinhThanhScope());
    }
}
