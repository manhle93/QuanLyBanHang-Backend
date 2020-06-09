<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TinhThanhScope;

class DiemChay extends Model
{
    protected $fillable = [
        'ten',
        'so_dien_thoai',
        'ten_nguoi_bao',
        'toa_nha_id',
        'dia_chi',
        'lat',
        'long',
        'trang_thai',
        'thoi_gian_bao_chay',
        'thoi_gian_bat_dau_xu_ly',
        'thoi_gian_ket_thuc',
        'mo_ta',
        'nguyen_nhan',
        'so_nguoi_chet',
        'so_nguoi_bi_thuong',
        'uoc_tinh_thiet_hai',
        'tinh_thanh_id',
        'cam_bien_id',
        'so_nguoi_tham_gia_chua_chay',
        'IMEI_thiet_bi',
        'cam_bien_tiep_theo'
    ];

    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha');
    }

    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh');
    }

    public function phuongTienPccc()
    {
        return $this->belongsToMany('App\PhuongTienPccc', 'c_t_phuong_tien_pccc_diem_chays');
    }
    public function donViHoTro()
    {
        return $this->belongsToMany('App\DonViHoTro', 'c_t_don_vi_ho_tro_diem_chays');
    }

    public function trangThaiDiemChay()
    {
        return $this->belongsTo('App\DanhMuc', 'trang_thai', 'ma')->withDefault();
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TinhThanhScope);
    }
    public function camBien()
    {
        return $this->belongsToMany('App\CamBien');
    }

    public function camBienFirst()
    {
        return $this->belongsTo('App\CamBien','cam_bien_id');
    }
}
