<?php

namespace App\Observers;

use App\NopTien;
use Exception;
use App\LichSuHoatDong;
use App\PhieuThu;

class NopTienCapObserver
{
    /**
     * Handle the nop tien "created" event.
     *
     * @param  \App\NopTien  $nopTien
     * @return void
     */
    public function created(NopTien $nopTien)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $nopTien->id,
                'type' => 'nop_tien',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => $nopTien->noi_dung . ". Số tiền: " . $nopTien->so_tien
            ]);
            if ($nopTien->trang_thai == 'nop_tien') {
                PhieuThu::create([
                    'type' => 'nop_tien',
                    'reference_id' => $nopTien->id,
                    'so_tien' => $nopTien->so_tien,
                    'noi_dung' => 'Thu tiền nộp tiền vào tài khoản khách hàng',
                    'thong_tin_giao_dich' => $nopTien->noi_dung . ". Mã giao dịch: " . $nopTien->ma,
                    'thong_tin_khach_hang' => null,
                    'user_id_khach_hang' => $nopTien->id_user_khach_hang
                ]);
            }
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the nop tien "updated" event.
     *
     * @param  \App\NopTien  $nopTien
     * @return void
     */
    public function updated(NopTien $nopTien)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $nopTien->id,
                'type' => 'nop_tien',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => $nopTien->noi_dung . ". Số tiền: " . $nopTien->so_tien
            ]);
            if ($nopTien->da_hoan_tien) {
                $phieuThu = PhieuThu::where('type', 'nop_tien')->where('reference_id', $nopTien->id)->first();
                if ($phieuThu) {
                    $phieuThu->delete();
                }
            }
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the nop tien "deleted" event.
     *
     * @param  \App\NopTien  $nopTien
     * @return void
     */
    public function deleted(NopTien $nopTien)
    {
        //
    }

    /**
     * Handle the nop tien "restored" event.
     *
     * @param  \App\NopTien  $nopTien
     * @return void
     */
    public function restored(NopTien $nopTien)
    {
        //
    }

    /**
     * Handle the nop tien "force deleted" event.
     *
     * @param  \App\NopTien  $nopTien
     * @return void
     */
    public function forceDeleted(NopTien $nopTien)
    {
        //
    }
}
