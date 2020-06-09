<?php

namespace App\Observers;

use App\CamBien;
use App\LichSuHoatDong;
use Exception;

class CamBienObserver
{
    /**
     * Handle the cam bien "created" event.
     *
     * @param  \App\CamBien  $camBien
     * @return void
     */
    public function created(CamBien $camBien)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $camBien->id,
                'type' => 'cam_bien',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới cảm biến'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the cam bien "updated" event.
     *
     * @param  \App\CamBien  $camBien
     * @return void
     */
    public function updated(CamBien $camBien)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $camBien->id,
                'type' => 'cam_bien',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin cảm biến'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the cam bien "deleted" event.
     *
     * @param  \App\CamBien  $camBien
     * @return void
     */
    public function deleted(CamBien $camBien)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $camBien->id,
                'type' => 'cam_bien',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa cảm biến '.$camBien->ma.', vị trí: '.$camBien->vi_tri
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the cam bien "restored" event.
     *
     * @param  \App\CamBien  $camBien
     * @return void
     */
    public function restored(CamBien $camBien)
    {
        //
    }

    /**
     * Handle the cam bien "force deleted" event.
     *
     * @param  \App\CamBien  $camBien
     * @return void
     */
    public function forceDeleted(CamBien $camBien)
    {
        //
    }
}
