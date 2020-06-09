<?php

namespace App\Observers;

use App\ThietBiQuay;
use App\LichSuHoatDong;
use Exception;

class ThietBiQuayObserver
{
    /**
     * Handle the thiet bi quay "created" event.
     *
     * @param  \App\ThietBiQuay  $thietBiQuay
     * @return void
     */
    public function created(ThietBiQuay $thietBiQuay)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thietBiQuay->id,
                'type' => 'thiet_bi_quay',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới thiết bị quay'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thiet bi quay "updated" event.
     *
     * @param  \App\ThietBiQuay  $thietBiQuay
     * @return void
     */
    public function updated(ThietBiQuay $thietBiQuay)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thietBiQuay->id,
                'type' => 'thiet_bi_quay',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin thiết bị quay'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thiet bi quay "deleted" event.
     *
     * @param  \App\ThietBiQuay  $thietBiQuay
     * @return void
     */
    public function deleted(ThietBiQuay $thietBiQuay)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thietBiQuay->id,
                'type' => 'thiet_bi',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa thiết bị '.$thietBiQuay->ten.', mã: '.$thietBiQuay->ma
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thiet bi quay "restored" event.
     *
     * @param  \App\ThietBiQuay  $thietBiQuay
     * @return void
     */
    public function restored(ThietBiQuay $thietBiQuay)
    {
        //
    }

    /**
     * Handle the thiet bi quay "force deleted" event.
     *
     * @param  \App\ThietBiQuay  $thietBiQuay
     * @return void
     */
    public function forceDeleted(ThietBiQuay $thietBiQuay)
    {
        //
    }
}
