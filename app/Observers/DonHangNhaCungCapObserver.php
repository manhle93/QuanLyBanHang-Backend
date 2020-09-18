<?php

namespace App\Observers;

use App\DonHangNhaCungCap;
use Exception;
use App\LichSuHoatDong;

class DonHangNhaCungCapObserver
{
    /**
     * Handle the don hang nha cung cap "created" event.
     *
     * @param  \App\DonHangNhaCungCap  $donHangNhaCungCap
     * @return void
     */
    public function created(DonHangNhaCungCap $donHangNhaCungCap)
    {
        try {
            $user = auth()->user();
            $noiDung = null;
            switch ($donHangNhaCungCap->trang_thai) {
                case 'nhap_kho':
                    $noiDung = 'Nhập kho đơn đặt hàng nhà cung cấp';
                    break;
                case 'huy_bo':
                    $noiDung = 'Hủy đơn đặt hàng nhà cung cấp';
                    break;
                case 'da_duyet':
                    $noiDung = 'Duyệt đơn đặt hàng nhà cung cấp';
                    break;
                case 'moi_tao':
                    $noiDung = 'Tạo đơn đặt hàng nhà cung cấp';
                    break;
                case 'nhap_kho_ngoai':
                    $noiDung = 'Mua hàng ngoài về nhập kho';
                    break;
                default:
                    $noiDung = 'tạo đơn hàng nhà cung cấp';
            }
            LichSuHoatDong::create([
                'reference_id' => $donHangNhaCungCap->id,
                'type' => 'don_hang_nha_cung_cap',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => $noiDung
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don hang nha cung cap "updated" event.
     *
     * @param  \App\DonHangNhaCungCap  $donHangNhaCungCap
     * @return void
     */
    public function updated(DonHangNhaCungCap $donHangNhaCungCap)
    {
        try {
            $user = auth()->user();
            $noiDung = null;
            switch ($donHangNhaCungCap->trang_thai) {
                case 'nhap_kho':
                    $noiDung = 'Nhập kho đơn đặt hàng nhà cung cấp';
                    break;
                case 'huy_bo':
                    $noiDung = 'Hủy đơn đặt hàng nhà cung cấp';
                    break;
                case 'da_duyet':
                    $noiDung = 'Duyệt đơn đặt hàng nhà cung cấp';
                    break;
                case 'moi_tao':
                    $noiDung = 'Tạo đơn đặt hàng nhà cung cấp';
                    break;
                case 'nhap_kho_ngoai':
                    $noiDung = 'Mua hàng ngoài về nhập kho';
                    break;
                default:
                    $noiDung = 'Cập nhật đơn hàng nhà cung cấp';
            }
            LichSuHoatDong::create([
                'reference_id' => $donHangNhaCungCap->id,
                'type' => 'don_hang_nha_cung_cap',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => $noiDung
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don hang nha cung cap "deleted" event.
     *
     * @param  \App\DonHangNhaCungCap  $donHangNhaCungCap
     * @return void
     */
    public function deleted(DonHangNhaCungCap $donHangNhaCungCap)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $donHangNhaCungCap->id,
                'type' => 'don_hang_nha_cung_cap',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa đơn hàng nhà cung cấp'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don hang nha cung cap "restored" event.
     *
     * @param  \App\DonHangNhaCungCap  $donHangNhaCungCap
     * @return void
     */
    public function restored(DonHangNhaCungCap $donHangNhaCungCap)
    {
        //
    }

    /**
     * Handle the don hang nha cung cap "force deleted" event.
     *
     * @param  \App\DonHangNhaCungCap  $donHangNhaCungCap
     * @return void
     */
    public function forceDeleted(DonHangNhaCungCap $donHangNhaCungCap)
    {
        //
    }
}
