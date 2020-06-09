<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CamBienResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'ma' => $this->ma,
            'IMEI_thiet_bi' => $this->IMEI_thiet_bi,
            'mo_ta' => $this->mo_ta,
            'created_at' => $this->created_at->format('d/m/Y'),
            'loai_cam_bien' => $this->loaiCamBien != null ? $this->loaiCamBien->ten : null,
            'loai_cam_bien_id' => $this->loai_cam_bien_id,
            'vi_tri' => $this->vi_tri,
            'so_lan' => $this->so_lan,
            'trang_thai_id' => $this->trang_thai_id,
            'trang_thai' => $this->trangThai != null ? $this->trangThai->ten : null,
            'ngay_trien_khai' => $this->ngay_trien_khai,
            'ngay_het_han' => $this->ngay_het_han,
            'thiet_bi' => $this->thietBi != null ? $this->thietBi->ten : null,
        ];
    }
}
