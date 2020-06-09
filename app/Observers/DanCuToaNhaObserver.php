<?php

namespace App\Observers;

use App\DanCu;
use App\LichSuHoatDong;
use Exception;
class DanCuToaNhaObserver
{
    /**
     * Handle the dan cu "created" event.
     *
     * @param  \App\DanCu  $danCu
     * @return void
     */
    public function created(DanCu $danCu)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $danCu->id,
                'type' => 'dan_cu',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới dân cư'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the dan cu "updated" event.
     *
     * @param  \App\DanCu  $danCu
     * @return void
     */
    public function updated(DanCu $danCu)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $danCu->id,
                'type' => 'dan_cu',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin dân cư'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the dan cu "deleted" event.
     *
     * @param  \App\DanCu  $danCu
     * @return void
     */
    public function deleted(DanCu $danCu)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $danCu->id,
                'type' => 'dan_cu',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa dân cư '. $danCu->name.'. ID tòa nhà: '.$danCu->toa_nha_id.'. ID đơn vị PCCC'.$danCu->don_vi_pccc_id
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the dan cu "restored" event.
     *
     * @param  \App\DanCu  $danCu
     * @return void
     */
    public function restored(DanCu $danCu)
    {
        //
    }

    /**
     * Handle the dan cu "force deleted" event.
     *
     * @param  \App\DanCu  $danCu
     * @return void
     */
    public function forceDeleted(DanCu $danCu)
    {
        //
    }
}
