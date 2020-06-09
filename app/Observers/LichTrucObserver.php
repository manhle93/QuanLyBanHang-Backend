<?php

namespace App\Observers;

use App\LichTruc;
use App\LichSuHoatDong;
use Exception;

class LichTrucObserver
{
    /**
     * Handle the lich truc "created" event.
     *
     * @param  \App\LichTruc  $lichTruc
     * @return void
     */
    public function created(LichTruc $lichTruc)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $lichTruc->id,
                'type' => 'lich_truc',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới lịch trực'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the lich truc "updated" event.
     *
     * @param  \App\LichTruc  $lichTruc
     * @return void
     */
    public function updated(LichTruc $lichTruc)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $lichTruc->id,
                'type' => 'lich_truc',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật lịch trực'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the lich truc "deleted" event.
     *
     * @param  \App\LichTruc  $lichTruc
     * @return void
     */
    public function deleted(LichTruc $lichTruc)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $lichTruc->id,
                'type' => 'lich_truc',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa lịch trực ngày '.$lichTruc->ngay_truc. '. ID lịch trực: '.$lichTruc->id
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the lich truc "restored" event.
     *
     * @param  \App\LichTruc  $lichTruc
     * @return void
     */
    public function restored(LichTruc $lichTruc)
    {
        //
    }

    /**
     * Handle the lich truc "force deleted" event.
     *
     * @param  \App\LichTruc  $lichTruc
     * @return void
     */
    public function forceDeleted(LichTruc $lichTruc)
    {
        //
    }
}
