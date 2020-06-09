<?php

namespace App\Observers;

use App\DiemLayNuoc;
use App\LichSuHoatDong;
use Exception;
use Illuminate\Support\Facades\Auth;

class DiemLayNuocObserver
{
    /**
     * Handle the diem lay nuoc "created" event.
     *
     * @param  \App\DiemLayNuoc  $diemLayNuoc
     * @return void
     */
    public function created(DiemLayNuoc $diemLayNuoc)
    {
        if (isset($diemLayNuoc)) {
            try {
                $user = Auth()->user();
                LichSuHoatDong::create([
                    'reference_id' => $diemLayNuoc->id,
                    'type' => 'diem_lay_nuoc',
                    'hanh_dong' => 'created',
                    'user_id' => $user->id,
                    'noi_dung' => 'Thêm mới điểm lấy nước'
                ]);
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    /**
     * Handle the diem lay nuoc "updated" event.
     *
     * @param  \App\DiemLayNuoc  $diemLayNuoc
     * @return void
     */
    public function updated(DiemLayNuoc $diemLayNuoc)
    {
        if (isset($diemLayNuoc)) {
            try {
                $user = auth()->user();
                LichSuHoatDong::create([
                    'reference_id' => $diemLayNuoc->id,
                    'type' => 'diem_lay_nuoc',
                    'hanh_dong' => 'updated',
                    'user_id' => $user->id,
                    'noi_dung' => 'Cập nhật thông tin điểm lấy nước'
                ]);
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    /**
     * Handle the diem lay nuoc "deleted" event.
     *
     * @param  \App\DiemLayNuoc  $diemLayNuoc
     * @return void
     */
    public function deleted(DiemLayNuoc $diemLayNuoc)
    {
        if (isset($diemLayNuoc)) {
            try {
                $user = Auth()->user();
                LichSuHoatDong::create([
                    'reference_id' => $diemLayNuoc->id,
                    'type' => 'diem_lay_nuoc',
                    'hanh_dong' => 'deleted',
                    'user_id' => $user->id,
                    'noi_dung' => 'Xóa điểm lấy nước '.$diemLayNuoc->ten.' Mã '.$diemLayNuoc->ma
                ]);
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    /**
     * Handle the diem lay nuoc "restored" event.
     *
     * @param  \App\DiemLayNuoc  $diemLayNuoc
     * @return void
     */
    public function restored(DiemLayNuoc $diemLayNuoc)
    {
        //
    }

    /**
     * Handle the diem lay nuoc "force deleted" event.
     *
     * @param  \App\DiemLayNuoc  $diemLayNuoc
     * @return void
     */
    public function forceDeleted(DiemLayNuoc $diemLayNuoc)
    {
        //
    }
}
