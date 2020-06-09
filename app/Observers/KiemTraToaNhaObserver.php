<?php

namespace App\Observers;

use App\KiemTraToaNha;
use App\LichSuHoatDong;
use Exception;

class KiemTraToaNhaObserver
{
    /**
     * Handle the kiem tra toa nha "created" event.
     *
     * @param  \App\KiemTraToaNha  $kiemTraToaNha
     * @return void
     */
    public function created(KiemTraToaNha $kiemTraToaNha)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $kiemTraToaNha->id,
                'type' => 'kiem_tra_toa_nha',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới kiểm tra cơ sở công trình'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the kiem tra toa nha "updated" event.
     *
     * @param  \App\KiemTraToaNha  $kiemTraToaNha
     * @return void
     */
    public function updated(KiemTraToaNha $kiemTraToaNha)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $kiemTraToaNha->id,
                'type' => 'kiem_tra_toa_nha',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin kiểm tra cơ sở công trình'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the kiem tra toa nha "deleted" event.
     *
     * @param  \App\KiemTraToaNha  $kiemTraToaNha
     * @return void
     */
    public function deleted(KiemTraToaNha $kiemTraToaNha)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $kiemTraToaNha->id,
                'type' => 'kiem_tra_toa_nha',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa kiểm tra cơ sở công trình, số quyết định: '.$kiemTraToaNha->quyet_dinh_kiem_tra. '. ID Tòa nhà: '.$kiemTraToaNha->toa_nha_id
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the kiem tra toa nha "restored" event.
     *
     * @param  \App\KiemTraToaNha  $kiemTraToaNha
     * @return void
     */
    public function restored(KiemTraToaNha $kiemTraToaNha)
    {
        //
    }

    /**
     * Handle the kiem tra toa nha "force deleted" event.
     *
     * @param  \App\KiemTraToaNha  $kiemTraToaNha
     * @return void
     */
    public function forceDeleted(KiemTraToaNha $kiemTraToaNha)
    {
        //
    }
}
