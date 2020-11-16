<?php

namespace App\Observers;

use App\DonDatHang;
use Exception;
use App\LichSuHoatDong;
use App\PhieuThu;

class DonDatHangObserver
{
    /**
     * Handle the don dat hang "created" event.
     *
     * @param  \App\DonDatHang  $donDatHang
     * @return void
     */
    public function created(DonDatHang $donDatHang)
    {
        try {
            $userLogin = auth()->user();
            $noiDung = null;
            switch ($donDatHang->trang_thai) {
                case 'moi_tao':
                    $noiDung = 'Tạo đơn đặt hàng';
                    break;
                case 'huy_hoa_don':
                    $noiDung = 'Hủy hóa đơn';
                    break;
                case 'hoa_don':
                    $noiDung = 'Tạo hóa đơn';
                    break;
                case 'huy_bo':
                    $noiDung = 'Hủy bỏ đơn hàng';
                    break;
                case 'mua_hang_online':
                    $noiDung = 'Khách hàng đặt hàng online';
                    break;
                case 'khach_huy':
                    $noiDung = 'Khách hàng hủy đơn đặt hàng online';
                    break;
                default:
                    $noiDung = 'Tạo đơn hàng';
            }
            LichSuHoatDong::create([
                'reference_id' => $donDatHang->id,
                'type' => 'don_hang',
                'hanh_dong' => 'created',
                'user_id' => $userLogin ? $userLogin->id : null,
                'noi_dung' => $noiDung
            ]);
            if ($donDatHang->da_thanh_toan > 0 ||  ($donDatHang->trang_thai == 'hoa_don' && ($donDatHang->thanh_toan == 'tien_mat' || $donDatHang->thanh_toan == 'chuyen_khoan' || $donDatHang->thanh_toan == 'cod'))) {
                $thanhToan = 'Thanh toán mua hàng';
                switch ($donDatHang->thanh_toan) {
                    case 'tien_mat':
                        $thanhToan = 'Thanh toán mua hàng bằng tiền mặt';
                        break;
                    case 'chuyen_khoan':
                        $thanhToan = 'Thanh toán mua hàng bằng chuyển khoản, quẹt thẻ';
                        break;
                    case 'tra_sau':
                        $thanhToan = 'Số tiền đã thanh toán cho đơn hàng trả sau';
                        break;
                    case 'cod':
                        $thanhToan = 'Thanh toán ship COD';
                        break;
                    default:
                        $thanhToan = 'Thanh toán mua hàng';
                }
                PhieuThu::create([
                    'user_id_nguoi_tao' => $userLogin->id,
                    'type' => 'hoa_don',
                    'reference_id' => $donDatHang->id,
                    'so_tien' => $donDatHang->da_thanh_toan,
                    'noi_dung' => $thanhToan,
                    'thong_tin_giao_dich' => null,
                    'thong_tin_khach_hang' => null,
                    'user_id_khach_hang' => $donDatHang->user_id ? $donDatHang->user_id : null
                ]);
            }
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don dat hang "updated" event.
     *
     * @param  \App\DonDatHang  $donDatHang
     * @return void
     */
    public function updated(DonDatHang $donDatHang)
    {
        try {
            $userLogin = auth()->user();
            $noiDung = null;
            switch ($donDatHang->trang_thai) {
                case 'moi_tao':
                    $noiDung = 'Cập nhật đơn đặt hàng';
                    break;
                case 'huy_hoa_don':
                    $noiDung = 'Hủy hóa đơn';
                    break;
                case 'hoa_don':
                    $noiDung = 'Chuyển hóa đơn bán hàng';
                    break;
                case 'huy_bo':
                    $noiDung = 'Hủy bỏ đơn hàng';
                    break;
                case 'mua_hang_online':
                    $noiDung = 'Khách hàng đặt hàng online';
                    break;
                case 'khach_huy':
                    $noiDung = 'Khách hàng hủy đơn đặt hàng online';
                    break;
                default:
                    $noiDung = 'Cập nhật đơn hàng';
            }
            LichSuHoatDong::create([
                'reference_id' => $donDatHang->id,
                'type' => 'don_hang',
                'hanh_dong' => 'updated',
                'user_id' => $userLogin ? $userLogin->id : null,
                'noi_dung' => $noiDung
            ]);
            if ($donDatHang->da_thanh_toan > 0 || ($donDatHang->trang_thai == 'hoa_don' && ($donDatHang->thanh_toan == 'tien_mat' || $donDatHang->thanh_toan == 'chuyen_khoan'))) {
                $phieuThu = PhieuThu::where('type', 'hoa_don')->where('reference_id', $donDatHang->id)->first();
                if ($phieuThu) {
                    $phieuThu->update([
                        'type' => 'hoa_don',
                        'reference_id' => $donDatHang->id,
                        'so_tien' => $donDatHang->da_thanh_toan,
                        'noi_dung' => $donDatHang->thanh_toan == 'tien_mat' ? 'Thanh toán mua hàng bằng tiền mặt' : 'Thanh toán mua hàng bằng chuyển khoản, quẹt thẻ',
                        'thong_tin_giao_dich' => null,
                        'thong_tin_khach_hang' => null,
                        'user_id_khach_hang' => $donDatHang->user_id ? $donDatHang->user_id : null
                    ]);
                } else {
                    PhieuThu::create([
                        'user_id_nguoi_tao' => $userLogin->id,
                        'type' => 'hoa_don',
                        'reference_id' => $donDatHang->id,
                        'so_tien' => $donDatHang->da_thanh_toan,
                        'noi_dung' => $donDatHang->thanh_toan == 'tien_mat' ? 'Thanh toán mua hàng bằng tiền mặt' : 'Thanh toán mua hàng bằng chuyển khoản, quẹt thẻ',
                        'thong_tin_giao_dich' => null,
                        'thong_tin_khach_hang' => null,
                        'user_id_khach_hang' => $donDatHang->user_id ? $donDatHang->user_id : null
                    ]);
                }
            } else {
                $phieuThu =  PhieuThu::where('type', 'hoa_don')->where('reference_id', $donDatHang->id)->first();
                if ($phieuThu) {
                    $phieuThu->delete();
                }
            }
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don dat hang "deleted" event.
     *
     * @param  \App\DonDatHang  $donDatHang
     * @return void
     */
    public function deleted(DonDatHang $donDatHang)
    {
        try {
            $userLogin = auth()->user();
            $noiDung = null;
            switch ($donDatHang->trang_thai) {
                case 'moi_tao':
                    $noiDung = 'Xóa đơn đặt hàng mới tạo';
                    break;
                case 'huy_hoa_don':
                    $noiDung = 'Xóa đơn hàng đã hủy hóa đơn';
                    break;
                case 'hoa_don':
                    $noiDung = 'Xóa hóa đơn bán hàng';
                    break;
                case 'huy_bo':
                    $noiDung = 'Xóa đơn hàng đã hủy bỏ';
                    break;
                case 'mua_hang_online':
                    $noiDung = 'Xóa đơn đặt hàng online từ khách hàng';
                    break;
                case 'khach_huy':
                    $noiDung = 'Xóa đơn hàng khách đã hủy';
                    break;
                default:
                    $noiDung = 'Xóa đơn hàng';
            }
            LichSuHoatDong::create([
                'reference_id' => $donDatHang->id,
                'type' => 'don_hang',
                'hanh_dong' => 'deleted',
                'user_id' => $userLogin ? $userLogin->id : null,
                'noi_dung' => $noiDung
            ]);
            $phieuThu =  PhieuThu::where('type', 'hoa_don')->where('reference_id', $donDatHang->id)->first();
            if ($phieuThu) {
                $phieuThu->delete();
            }
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the don dat hang "restored" event.
     *
     * @param  \App\DonDatHang  $donDatHang
     * @return void
     */
    public function restored(DonDatHang $donDatHang)
    {
        //
    }

    /**
     * Handle the don dat hang "force deleted" event.
     *
     * @param  \App\DonDatHang  $donDatHang
     * @return void
     */
    public function forceDeleted(DonDatHang $donDatHang)
    {
        //
    }
}
