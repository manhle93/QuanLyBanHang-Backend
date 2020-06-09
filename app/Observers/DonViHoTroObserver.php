<?php

namespace App\Observers;

use App\DonViHoTro;
use App\LichSuHoatDong;
use Exception;

class DonViHoTroObserver
{
    /**
     * Handle the don vi ho tro "created" event.
     *
     * @param  \App\DonViHoTro  $donViHoTro
     * @return void
     */
    public function created(DonViHoTro $donViHoTro)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $donViHoTro->id,
                'type' => 'don_vi_ho_tro',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới đơn vị hỗ trợ'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don vi ho tro "updated" event.
     *
     * @param  \App\DonViHoTro  $donViHoTro
     * @return void
     */
    public function updated(DonViHoTro $donViHoTro)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $donViHoTro->id,
                'type' => 'don_vi_ho_tro',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin đơn vị hỗ trợ'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don vi ho tro "deleted" event.
     *
     * @param  \App\DonViHoTro  $donViHoTro
     * @return void
     */
    public function deleted(DonViHoTro $donViHoTro)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $donViHoTro->id,
                'type' => 'don_vi_ho_tro',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa đơn vị hỗ trợ '.$donViHoTro->ten
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don vi ho tro "restored" event.
     *
     * @param  \App\DonViHoTro  $donViHoTro
     * @return void
     */
    public function restored(DonViHoTro $donViHoTro)
    {
        //
    }

    /**
     * Handle the don vi ho tro "force deleted" event.
     *
     * @param  \App\DonViHoTro  $donViHoTro
     * @return void
     */
    public function forceDeleted(DonViHoTro $donViHoTro)
    {
        //
    }
}
