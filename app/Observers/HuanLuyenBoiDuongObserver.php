<?php

namespace App\Observers;

use App\HuanLuyenBoiDuong;
use App\LichSuHoatDong;
use Exception;
class HuanLuyenBoiDuongObserver
{
    /**
     * Handle the huan luyen boi duong "created" event.
     *
     * @param  \App\HuanLuyenBoiDuong  $huanLuyenBoiDuong
     * @return void
     */
    public function created(HuanLuyenBoiDuong $huanLuyenBoiDuong)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $huanLuyenBoiDuong->id,
                'type' => 'huan_luyen_boi_duong',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới công tác huấn luyện, bồi dưỡng nghiệp vụ PCCC'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the huan luyen boi duong "updated" event.
     *
     * @param  \App\HuanLuyenBoiDuong  $huanLuyenBoiDuong
     * @return void
     */
    public function updated(HuanLuyenBoiDuong $huanLuyenBoiDuong)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $huanLuyenBoiDuong->id,
                'type' => 'huan_luyen_boi_duong',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật công tác huấn luyện, bồi dưỡng nghiệp vụ PCCC'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the huan luyen boi duong "deleted" event.
     *
     * @param  \App\HuanLuyenBoiDuong  $huanLuyenBoiDuong
     * @return void
     */
    public function deleted(HuanLuyenBoiDuong $huanLuyenBoiDuong)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $huanLuyenBoiDuong->id,
                'type' => 'huan_luyen_boi_duong',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa công tác huấn luyện, bồi dưỡng nghiệp vụ PCCC. Số giấy chứng nhận '.$huanLuyenBoiDuong->so_giay_cn.'. ID tòa nhà: '.$huanLuyenBoiDuong->toa_nha_id
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the huan luyen boi duong "restored" event.
     *
     * @param  \App\HuanLuyenBoiDuong  $huanLuyenBoiDuong
     * @return void
     */
    public function restored(HuanLuyenBoiDuong $huanLuyenBoiDuong)
    {
        //
    }

    /**
     * Handle the huan luyen boi duong "force deleted" event.
     *
     * @param  \App\HuanLuyenBoiDuong  $huanLuyenBoiDuong
     * @return void
     */
    public function forceDeleted(HuanLuyenBoiDuong $huanLuyenBoiDuong)
    {
        //
    }
}
