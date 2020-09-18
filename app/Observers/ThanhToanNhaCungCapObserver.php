<?php

namespace App\Observers;

use App\ThanhToanNhaCungCap;
use Exception;
use App\LichSuHoatDong;

class ThanhToanNhaCungCapObserver
{
    /**
     * Handle the thanh toan nha cung cap "created" event.
     *
     * @param  \App\ThanhToanNhaCungCap  $thanhToanNhaCungCap
     * @return void
     */
    public function created(ThanhToanNhaCungCap $thanhToanNhaCungCap)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thanhToanNhaCungCap->id,
                'type' => 'thanh_toan_nha_cung_cap',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Tạo thanh toán cho nhà cung cấp'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thanh toan nha cung cap "updated" event.
     *
     * @param  \App\ThanhToanNhaCungCap  $thanhToanNhaCungCap
     * @return void
     */
    public function updated(ThanhToanNhaCungCap $thanhToanNhaCungCap)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thanhToanNhaCungCap->id,
                'type' => 'thanh_toan_nha_cung_cap',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật thông tin thanh toán cho nhà cung cấp'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thanh toan nha cung cap "deleted" event.
     *
     * @param  \App\ThanhToanNhaCungCap  $thanhToanNhaCungCap
     * @return void
     */
    public function deleted(ThanhToanNhaCungCap $thanhToanNhaCungCap)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thanhToanNhaCungCap->id,
                'type' => 'thanh_toan_nha_cung_cap',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa thanh toán cho nhà cung cấp'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thanh toan nha cung cap "restored" event.
     *
     * @param  \App\ThanhToanNhaCungCap  $thanhToanNhaCungCap
     * @return void
     */
    public function restored(ThanhToanNhaCungCap $thanhToanNhaCungCap)
    {
        //
    }

    /**
     * Handle the thanh toan nha cung cap "force deleted" event.
     *
     * @param  \App\ThanhToanNhaCungCap  $thanhToanNhaCungCap
     * @return void
     */
    public function forceDeleted(ThanhToanNhaCungCap $thanhToanNhaCungCap)
    {
        //
    }
}
