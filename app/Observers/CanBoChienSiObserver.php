<?php

namespace App\Observers;

use App\CanBoChienSi;
use App\LichSuHoatDong;
use Exception;

class CanBoChienSiObserver
{
    /**
     * Handle the can bo chien si "created" event.
     *
     * @param  \App\CanBoChienSi  $canBoChienSi
     * @return void
     */
    public function created(CanBoChienSi $canBoChienSi)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $canBoChienSi->id,
                'type' => 'can_bo_chien_si',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới cán bộ chiến sĩ'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the can bo chien si "updated" event.
     *
     * @param  \App\CanBoChienSi  $canBoChienSi
     * @return void
     */
    public function updated(CanBoChienSi $canBoChienSi)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $canBoChienSi->id,
                'type' => 'can_bo_chien_si',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin cán bộ chiến sĩ'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the can bo chien si "deleted" event.
     *
     * @param  \App\CanBoChienSi  $canBoChienSi
     * @return void
     */
    public function deleted(CanBoChienSi $canBoChienSi)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $canBoChienSi->id,
                'type' => 'can_bo_chien_si',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa cán bộ chiến sĩ tên ' . $canBoChienSi->ten
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the can bo chien si "restored" event.
     *
     * @param  \App\CanBoChienSi  $canBoChienSi
     * @return void
     */
    public function restored(CanBoChienSi $canBoChienSi)
    {
        //
    }

    /**
     * Handle the can bo chien si "force deleted" event.
     *
     * @param  \App\CanBoChienSi  $canBoChienSi
     * @return void
     */
    public function forceDeleted(CanBoChienSi $canBoChienSi)
    {
        //
    }
}
