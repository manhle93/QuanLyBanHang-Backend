<?php

namespace App\Observers;

use App\DonViPccc;
use App\LichSuHoatDong;
use Exception;

class DonViPcccObserver
{
    /**
     * Handle the don vi pccc "created" event.
     *
     * @param  \App\DonViPccc  $donViPccc
     * @return void
     */
    public function created(DonViPccc $donViPccc)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $donViPccc->id,
                'type' => 'don_vi_pccc',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới đơn vị PCCC'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don vi pccc "updated" event.
     *
     * @param  \App\DonViPccc  $donViPccc
     * @return void
     */
    public function updated(DonViPccc $donViPccc)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $donViPccc->id,
                'type' => 'don_vi_pccc',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật đơn vị PCCC'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don vi pccc "deleted" event.
     *
     * @param  \App\DonViPccc  $donViPccc
     * @return void
     */
    public function deleted(DonViPccc $donViPccc)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $donViPccc->id,
                'type' => 'don_vi_pccc',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa đơn vị PCCC '.$donViPccc->ten
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don vi pccc "restored" event.
     *
     * @param  \App\DonViPccc  $donViPccc
     * @return void
     */
    public function restored(DonViPccc $donViPccc)
    {
        //
    }

    /**
     * Handle the don vi pccc "force deleted" event.
     *
     * @param  \App\DonViPccc  $donViPccc
     * @return void
     */
    public function forceDeleted(DonViPccc $donViPccc)
    {
        //
    }
}
