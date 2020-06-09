<?php

namespace App\Observers;

use App\LichSuHoatDong;
use App\ToaNha;
use Exception;

class ToaNhaObserver
{
    /**
     * Handle the toa nha "created" event.
     *
     * @param  \App\ToaNha  $toaNha
     * @return void
     */
    public function created(ToaNha $toaNha)
    {
        if (isset($toaNha)) {
            try {
                $user = auth()->user();
                LichSuHoatDong::create([
                    'reference_id' => $toaNha->id,
                    'type' => 'toa_nha',
                    'hanh_dong' => 'created',
                    'user_id' => $user->id,
                    'noi_dung' => 'Thêm mới cơ sở công trình'
                ]);
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    /**
     * Handle the toa nha "updated" event.
     *
     * @param  \App\ToaNha  $toaNha
     * @return void
     */
    public function updated(ToaNha $toaNha)
    {
        if (isset($toaNha)) {
            try {
                $user = auth()->user();
                LichSuHoatDong::create([
                    'reference_id' => $toaNha->id,
                    'type' => 'toa_nha',
                    'hanh_dong' => 'updated',
                    'user_id' => $user->id,
                    'noi_dung' => 'Cập nhật thông tin cơ sở công trình'
                ]);
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    /**
     * Handle the toa nha "deleted" event.
     *
     * @param  \App\ToaNha  $toaNha
     * @return void
     */
    public function deleted(ToaNha $toaNha)
    {
        if (isset($toaNha)) {
            try {
                $user = auth()->user();
                LichSuHoatDong::create([
                    'reference_id' => $toaNha->id,
                    'type' => 'toa_nha',
                    'hanh_dong' => 'deleted',
                    'user_id' => $user->id,
                    'noi_dung' => 'Xóa cơ sở công trình '.$toaNha->ten.' - Mã tòa nhà '.$toaNha->ma
                ]);
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    /**
     * Handle the toa nha "restored" event.
     *
     * @param  \App\ToaNha  $toaNha
     * @return void
     */
    public function restored(ToaNha $toaNha)
    {
        //
    }

    /**
     * Handle the toa nha "force deleted" event.
     *
     * @param  \App\ToaNha  $toaNha
     * @return void
     */
    public function forceDeleted(ToaNha $toaNha)
    {
        //
    }
}
