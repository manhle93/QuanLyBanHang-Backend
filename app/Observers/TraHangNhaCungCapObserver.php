<?php

namespace App\Observers;

use App\TraHangNhaCungCap;
use Exception;
use App\LichSuHoatDong;
class TraHangNhaCungCapObserver
{
    /**
     * Handle the tra hang nha cung cap "created" event.
     *
     * @param  \App\TraHangNhaCungCap  $traHangNhaCungCap
     * @return void
     */
    public function created(TraHangNhaCungCap $traHangNhaCungCap)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $traHangNhaCungCap->id,
                'type' => 'tra_hang_nha_cung_cap',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Tạo đơn trả hàng cho nhà cung cấp'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the tra hang nha cung cap "updated" event.
     *
     * @param  \App\TraHangNhaCungCap  $traHangNhaCungCap
     * @return void
     */
    public function updated(TraHangNhaCungCap $traHangNhaCungCap)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $traHangNhaCungCap->id,
                'type' => 'tra_hang_nha_cung_cap',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin đơn trả hàng cho nhà cung cấp'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the tra hang nha cung cap "deleted" event.
     *
     * @param  \App\TraHangNhaCungCap  $traHangNhaCungCap
     * @return void
     */
    public function deleted(TraHangNhaCungCap $traHangNhaCungCap)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $traHangNhaCungCap->id,
                'type' => 'tra_hang_nha_cung_cap',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa đơn trả hàng cho nhà cung cấp'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the tra hang nha cung cap "restored" event.
     *
     * @param  \App\TraHangNhaCungCap  $traHangNhaCungCap
     * @return void
     */
    public function restored(TraHangNhaCungCap $traHangNhaCungCap)
    {
        //
    }

    /**
     * Handle the tra hang nha cung cap "force deleted" event.
     *
     * @param  \App\TraHangNhaCungCap  $traHangNhaCungCap
     * @return void
     */
    public function forceDeleted(TraHangNhaCungCap $traHangNhaCungCap)
    {
        //
    }
}
