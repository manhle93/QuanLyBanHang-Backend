<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThietBiResource extends JsonResource
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
            'id' => $this->id,
            "ten" => $this->ten,
            "ma" => $this->ma,
            "imei" => $this->imei,
            "mo_ta" => $this->mo_ta,
            "loai_thiet_bi_id" => $this->loai_thiet_bi_id,
            "loai_thiet_bi" => $this->loai_thiet_bi_id != null ? $this->loaiThietBi->ten : null,
            "toa_nha_id" => $this->toa_nha_id,
            "toa_nha" => $this->toa_nha_id != null ? $this->toaNha->ten : null,
            "ngay_trien_khai" => $this->ngay_trien_khai,
            "ngay_het_han" => $this->ngay_het_han,
            "trang_thai" => $this->trang_thai,
            "dia_chi" => $this->dia_chi,
            "user_id" => $this->user_id,
            "cam_bien" => $this->camBien,
            'tinh_thanh' => $this->toaNha ? $this->toaNha->tinhThanh->name : null,
            'don_vi_pccc' => $this->toaNha ? $this->toaNha->donViPccc->ten : null,
            'online' => $this->online
        ];
    }
}
