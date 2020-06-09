<?php

namespace App\Observers;

use App\ThucTapPhuongAnChuaChay;
use Exception;
use App\LichSuHoatDong;

class ThucTapPhuongAnChuaChayObserver
{
    /**
     * Handle the thuc tap phuong an chua chay "created" event.
     *
     * @param  \App\ThucTapPhuongAnChuaChay  $thucTapPhuongAnChuaChay
     * @return void
     */
    public function created(ThucTapPhuongAnChuaChay $thucTapPhuongAnChuaChay)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thucTapPhuongAnChuaChay->id,
                'type' => 'thiet_bi_quay',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới công tác xây dựng thực tập phương án chữa cháy'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thuc tap phuong an chua chay "updated" event.
     *
     * @param  \App\ThucTapPhuongAnChuaChay  $thucTapPhuongAnChuaChay
     * @return void
     */
    public function updated(ThucTapPhuongAnChuaChay $thucTapPhuongAnChuaChay)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thucTapPhuongAnChuaChay->id,
                'type' => 'thiet_bi_quay',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật công tác xây dựng thực tập phương án chữa cháy'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thuc tap phuong an chua chay "deleted" event.
     *
     * @param  \App\ThucTapPhuongAnChuaChay  $thucTapPhuongAnChuaChay
     * @return void
     */
    public function deleted(ThucTapPhuongAnChuaChay $thucTapPhuongAnChuaChay)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thucTapPhuongAnChuaChay->id,
                'type' => 'thiet_bi_quay',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa công tác xây dựng thực tập phương án chữa cháy'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the thuc tap phuong an chua chay "restored" event.
     *
     * @param  \App\ThucTapPhuongAnChuaChay  $thucTapPhuongAnChuaChay
     * @return void
     */
    public function restored(ThucTapPhuongAnChuaChay $thucTapPhuongAnChuaChay)
    {
        //
    }

    /**
     * Handle the thuc tap phuong an chua chay "force deleted" event.
     *
     * @param  \App\ThucTapPhuongAnChuaChay  $thucTapPhuongAnChuaChay
     * @return void
     */
    public function forceDeleted(ThucTapPhuongAnChuaChay $thucTapPhuongAnChuaChay)
    {
        //
    }
}
