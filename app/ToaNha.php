<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TinhThanhScope;

class ToaNha extends Model
{
    protected $guarded = [];

    // protected $fillable = [
    //     'ma', 'ten', 'dia_chi', 'lat', 'long', 'tinh_thanh_id', 'quan_huyen_id',
    //     'huong_vao_toa_nha',
    //     'loai_hinh_so_huu_id',
    //     'hien_thi_toa_nha',
    //     'don_vi_pccc_id',
    //     'chu_toa_nha',
    //     'ngay_dang_ki_kd',
    //     'ngay_het_han_kd',
    //     'dien_thoai',
    //     'sl_cam_bien',
    //     'search'
    // ];

    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh', 'tinh_thanh_id')->select('id', 'name', 'code')->withDefault();
    }

    public function quanHuyen()
    {
        return $this->belongsTo('App\QuanHuyen', 'quan_huyen_id')->select('id', 'name', 'code', 'tinh_thanh_id')->withDefault();
    }

    public function donViPccc()
    {
        return $this->belongsTo('App\DonViPccc', 'don_vi_pccc_id')->withDefault();
    }

    public function loaiHinhSoHuu()
    {
        return $this->belongsTo('App\DanhMuc', 'loai_hinh_so_huu_id')->withDefault();
    }

    public function files()
    {
        return $this->hasMany('App\File', 'reference_id')->where('type', 'toa_nha');;
    }

    public function thietBi()
    {
        return $this->hasMany('App\ThietBi');
    }
    public function soDienThoai()
    {
        return $this->hasMany('App\SoDienThoaiToaNha', 'toa_nha_id', 'id');
    }
    public function camBien()
    {
        return $this->hasManyThrough('App\CamBien', 'App\ThietBi');
    }

    public function kiemTraToaNhas()
    {
        return $this->hasMany('App\KiemTraToaNha');
    }
    public function thamDuyets()
    {
        return $this->hasMany('App\ThamDinhPheDuyet', 'toa_nha_id', 'id');
    }
    public function thayDoiPcccs()
    {
        return $this->hasMany('App\ToaNhaThayDoiPccc', 'toa_nha_id', 'id');
    }
    public function viPhams()
    {
        return $this->hasMany('App\XuLyViPham', 'toa_nha_id', 'id');
    }
    public function huanLuyens()
    {
        return $this->hasMany('App\HuanLuyenBoiDuong', 'toa_nha_id', 'id');
    }
    public function vuChays()
    {
        return $this->hasMany('App\DiemChay', 'toa_nha_id', 'id');
    }
    public function pcccCoSo()
    {
        return $this->hasMany('App\PcccCoSoToaNha', 'toa_nha_id', 'id');
    }
    public function phuongTien()
    {
        return $this->hasMany('App\PhuongTienToaNha', 'toa_nha_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TinhThanhScope());
    }
}
