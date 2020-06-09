<?php

namespace App\Observers;

use App\CuuHoCuuNan;
use App\LichSuHoatDong;
use Exception;

class CuuHoCuuNanObserver
{
    /**
     * Handle the cuu ho cuu nan "created" event.
     *
     * @param  \App\CuuHoCuuNan  $cuuHoCuuNan
     * @return void
     */
    public function created(CuuHoCuuNan $cuuHoCuuNan)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $cuuHoCuuNan->id,
                'type' => 'cuu_ho_cuu_nan',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới vụ việc cứu nạn cứu hộ'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the cuu ho cuu nan "updated" event.
     *
     * @param  \App\CuuHoCuuNan  $cuuHoCuuNan
     * @return void
     */
    public function updated(CuuHoCuuNan $cuuHoCuuNan)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $cuuHoCuuNan->id,
                'type' => 'cuu_ho_cuu_nan',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin cứu nạn cứu hộ'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the cuu ho cuu nan "deleted" event.
     *
     * @param  \App\CuuHoCuuNan  $cuuHoCuuNan
     * @return void
     */
    public function deleted(CuuHoCuuNan $cuuHoCuuNan)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $cuuHoCuuNan->id,
                'type' => 'cuu_ho_cuu_nan',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa cứu hộ cứu nạn ' . $cuuHoCuuNan->ten
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the cuu ho cuu nan "restored" event.
     *
     * @param  \App\CuuHoCuuNan  $cuuHoCuuNan
     * @return void
     */
    public function restored(CuuHoCuuNan $cuuHoCuuNan)
    {
        //
    }

    /**
     * Handle the cuu ho cuu nan "force deleted" event.
     *
     * @param  \App\CuuHoCuuNan  $cuuHoCuuNan
     * @return void
     */
    public function forceDeleted(CuuHoCuuNan $cuuHoCuuNan)
    {
        //
    }
}
