<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PhuongTienPcccResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "bien_so" => $this->bien_so,
            "ten" => $this->ten,
            "loai_phuong_tien_pccc_id" => $this->loai_phuong_tien_pccc_id,
            "so_dien_thoai" => $this->so_dien_thoai,
            "don_vi_pccc_id" => $this->don_vi_pccc_id,
            "so_hieu" => $this->so_hieu,
            "lat" => $this->lat,
            "long" => $this->long,
            "tinh_thanh_id" => $this->tinh_thanh_id,
            "loai_phuong_tien_pccc" => !empty($this->loaiPhuongTienPccc->ten) ? $this->loaiPhuongTienPccc->ten : null,
            "don_vi_pccc" => !empty($this->donViPccc->ten) ? $this->donViPccc->ten : null,
            "imei" => $this->imei,
            "trang_thai_hoat_dong" => $this->trang_thai_hoat_dong,
            "quan_huyen_id" => $this->quan_huyen_id
        ];
    }
}
