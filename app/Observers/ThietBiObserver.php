<?php

namespace App\Observers;

use App\ThietBi;
use App\LichSuHoatDong;
use Exception;

class ThietBiObserver
{
    /**
     * Handle the thiet bi "created" event.
     *
     * @param  \App\ThietBi  $thietBi
     * @return void
     */
    public function created(ThietBi $thietBi)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thietBi->id,
                'type' => 'thiet_bi',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới thiết bị'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thiet bi "updated" event.
     *
     * @param  \App\ThietBi  $thietBi
     * @return void
     */
    public function updated(ThietBi $thietBi)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thietBi->id,
                'type' => 'thiet_bi',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin thiết bị'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thiet bi "deleted" event.
     *
     * @param  \App\ThietBi  $thietBi
     * @return void
     */
    public function deleted(ThietBi $thietBi)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thietBi->id,
                'type' => 'thiet_bi',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa thiết bị '.$thietBi->ten.', imei: '.$thietBi->imei
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thiet bi "restored" event.
     *
     * @param  \App\ThietBi  $thietBi
     * @return void
     */
    public function restored(ThietBi $thietBi)
    {
        //
    }

    /**
     * Handle the thiet bi "force deleted" event.
     *
     * @param  \App\ThietBi  $thietBi
     * @return void
     */
    public function forceDeleted(ThietBi $thietBi)
    {
        //
    }
}
