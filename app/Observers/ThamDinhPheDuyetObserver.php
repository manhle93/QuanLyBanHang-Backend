<?php

namespace App\Observers;

use App\ThamDinhPheDuyet;
use App\LichSuHoatDong;
use Exception;
class ThamDinhPheDuyetObserver
{
    /**
     * Handle the tham dinh phe duyet "created" event.
     *
     * @param  \App\ThamDinhPheDuyet  $thamDinhPheDuyet
     * @return void
     */
    public function created(ThamDinhPheDuyet $thamDinhPheDuyet)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thamDinhPheDuyet->id,
                'type' => 'tham_duyet',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới công tác thẩm định và cấp giấy phép chứng nhận đủ điều kiện về PCCC'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the tham dinh phe duyet "updated" event.
     *
     * @param  \App\ThamDinhPheDuyet  $thamDinhPheDuyet
     * @return void
     */
    public function updated(ThamDinhPheDuyet $thamDinhPheDuyet)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thamDinhPheDuyet->id,
                'type' => 'tham_duyet',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật công tác thẩm định và cấp giấy phép chứng nhận đủ điều kiện về PCCC'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the tham dinh phe duyet "deleted" event.
     *
     * @param  \App\ThamDinhPheDuyet  $thamDinhPheDuyet
     * @return void
     */
    public function deleted(ThamDinhPheDuyet $thamDinhPheDuyet)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $thamDinhPheDuyet->id,
                'type' => 'tham_duyet',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa công tác thẩm định và cấp giấy phép chứng nhận đủ điều kiện về PCCC. Số văn bản: '.$thamDinhPheDuyet->so_van_ban.'. Tên văn bản: '.$thamDinhPheDuyet->ten_van_ban
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the tham dinh phe duyet "restored" event.
     *
     * @param  \App\ThamDinhPheDuyet  $thamDinhPheDuyet
     * @return void
     */
    public function restored(ThamDinhPheDuyet $thamDinhPheDuyet)
    {
        //
    }

    /**
     * Handle the tham dinh phe duyet "force deleted" event.
     *
     * @param  \App\ThamDinhPheDuyet  $thamDinhPheDuyet
     * @return void
     */
    public function forceDeleted(ThamDinhPheDuyet $thamDinhPheDuyet)
    {
        //
    }
}
