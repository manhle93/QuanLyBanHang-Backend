<?php

namespace App\Observers;

use App\PhuongTienPCCC;
use App\LichSuHoatDong;
use Exception;

class PhuongTienObserver
{
    /**
     * Handle the phuong tien p c c c "created" event.
     *
     * @param  \App\PhuongTienPCCC  $phuongTienPCCC
     * @return void
     */
    public function created(PhuongTienPCCC $phuongTienPCCC)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $phuongTienPCCC->id,
                'type' => 'phuong_tien_pccc',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới phương tiện PCCC'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the phuong tien p c c c "updated" event.
     *
     * @param  \App\PhuongTienPCCC  $phuongTienPCCC
     * @return void
     */
    public function updated(PhuongTienPCCC $phuongTienPCCC)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $phuongTienPCCC->id,
                'type' => 'phuong_tien_pccc',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin phương tiện PCCC'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the phuong tien p c c c "deleted" event.
     *
     * @param  \App\PhuongTienPCCC  $phuongTienPCCC
     * @return void
     */
    public function deleted(PhuongTienPCCC $phuongTienPCCC)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $phuongTienPCCC->id,
                'type' => 'phuong_tien_pccc',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa phương tiện PCCC '.$phuongTienPCCC->ten.', biển số '.$phuongTienPCCC->bien_so
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the phuong tien p c c c "restored" event.
     *
     * @param  \App\PhuongTienPCCC  $phuongTienPCCC
     * @return void
     */
    public function restored(PhuongTienPCCC $phuongTienPCCC)
    {
        //
    }

    /**
     * Handle the phuong tien p c c c "force deleted" event.
     *
     * @param  \App\PhuongTienPCCC  $phuongTienPCCC
     * @return void
     */
    public function forceDeleted(PhuongTienPCCC $phuongTienPCCC)
    {
        //
    }
}
