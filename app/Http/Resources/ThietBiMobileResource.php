<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThietBiMobileResource extends JsonResource
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
            'ten_loai' => $this->loai,
            'ma_loai' => $this->ma_loai,
            'imei' => $this->imei ? $this->imei : $this->IMEI_thiet_bi,
            'ma' => $this->ma,
            'online' => $this->online,
            'battery' => $this->battery,
            'dia_chi' => $this->dia_chi,
            'chu_so_huu' => $this->chu_so_huu
        ];
    }
}
