<?php

namespace App\Http\Controllers;

use App\DoiTraHang;
use App\DonDatHang;
use App\SanPham;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Excel;
use App\Traits\ExecuteExcel;
use App\SanPhamDonDatHang;
use App\User;
use Carbon\Carbon;
use OneSignal;
use PhpParser\Node\Stmt\Do_;

class BaoCaoController extends Controller
{
    use ExecuteExcel;

    public function senNotify()
    {
        OneSignal::sendNotificationUsingTags(
            "VKL Thiết bị đã offline!",
            array(["field" => "tag", "relation" => "=", "key" => "user_id", "value" => 57]),
            $url = null,
            $data = ['type' => 'task_new', 'id' => 57]
        );
        return response(['message' => 'Thanh cong'], 200);
    }

    public function getPolygon(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $tinh_thanh = null;
        if (isset($tinh_thanh_id)) {
            $tinh_thanh = TinhThanh::where('id', $tinh_thanh_id)->select(DB::raw('st_asgeojson(geom)'))->first();
        }
        return response()->json([
            'data' => $tinh_thanh,
            'code' => 200,
            'message' => 'Thành công'
        ], 200);
    }
    public function getThongmobile(Request $request)
    {
        $user = auth()->user();
        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 10);
        $thietBis = [];
        if ($user && ($user->role_id == 3 || $user->role_id == 4) && $user->toa_nha_id) {
            $thietBiToaNha = ThietBi::where('toa_nha_id', $user->toa_nha_id)->pluck('id');
            $thietBis = ThongBaoTrangThaiThietBi::with('toaNha', 'tinhThanh')->where('toa_nha_id', $user->toa_nha_id)->whereIn('thiet_bi_id', $thietBiToaNha)->orderBy('updated_at', 'DESC')->paginate($perPage, ['*'], 'page', $page);
        }
        return response($thietBis, 200);
    }

    public function getBaoCaoBanHang(Request $request)
    {
        $date = $request->get('date');
        $typeOrder = $request->get('don_hang');
        $orderBy = $request->get('orderBy');
        $khachHang = $request->get('khach_hang');
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = DonDatHang::with('user:id', 'traHang');
        // TH mua hang 
        if (isset($orderBy) && $orderBy == 'ban_hang') {
            $query = $query->where('trang_thai', 'hoa_don');
        } elseif (isset($orderBy) && $orderBy == 'tra_hang') {
            // TH tra hang
            $query = $query->orWhereHas('traHang', function ($query) use ($orderBy) {
                $query->where('type', $orderBy);
            });
        }
        // Tim kiem theo ngay
        if (isset($date) && count($date)) {
            $query = $query->whereBetween('order_date', [Carbon::parse($date[0])->addHours(7), Carbon::parse($date[1])->addHours(31)]);
        }
        // Tim kiem theo khach hang
        // if (isset($khachHang)) {
        //     $query = $query->orWhereHas('user', function ($query) use ($khachHang) {
        //                     $query->where('id', $khachHang);
        //     });

        // }
        if (isset($khachHang)) {
            $query = $query->where('user_id', $khachHang);
        }

        // Tinh tong hoa don/don dat hang
        return response()->json([
            'data' => $query->get(),
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
    public function downloadBaoCaoBanHang(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $trangThai = $request->get('don_hang');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        $allDonHang = DonDatHang::pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
            $allDonHang = DonDatHang::where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }
        $diemchay_data = SanPhamDonDatHang::whereIn('don_dat_hang_id', $allDonHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if ($trangThai == 'hoa_don') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if ($trangThai == 'don_dat_hang') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $diemchay_data =  $diemchay_data->orderBy('doanh_thu', 'desc')->get();
        } else {
            $diemchay_data =  $diemchay_data->orderBy($orderBy, 'desc')->get();
        }

        $diemchay_array[] = array('STT', 'Thời gian', 'Sản phẩm hàng hóa', 'Giá bán', 'Số lượng', 'Doanh thu');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Thời gian'  => $diemchay->created_at,
                'Sản phẩm hàng hóa'  => $diemchay->sanPham ? $diemchay->sanPham->ten_san_pham : "",
                'Giá bán' => $diemchay->sanPham ? $diemchay->gia_ban . ' /' . $diemchay->sanPham->don_vi_tinh : $diemchay->gia_ban,
                'Số lượng' => $diemchay->sanPham ? $diemchay->so_luong . " " . $diemchay->sanPham->don_vi_tinh :  $diemchay->so_luong,
                'Doanh thu' => $diemchay->doanh_thu ? $diemchay->doanh_thu : $diemchay->gia_ban * $diemchay->so_luong,
            );
        }
        \Excel::create('Báo cáo', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Báo cáo');
            $excel->sheet('Báo cáo', function ($sheet) use ($diemchay_array) {
                $sheet->fromArray($diemchay_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($diemchay_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));
                    if ($i == 0) {
                        foreach ($diemchay_array as $key => $diemchay) {
                            $sheet->getStyle($j . (++$key))->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );
                        }
                    }
                    $j++;
                }
            });
        })->download('xlsx');
    }

    public function getBaoCaoDatHang(Request $request)
    {
        $date = $request->get('date');
        $trangthai = $request->get('orderBy');
        $don_hang = $request->get('don_hang');
        $khachHang = $request->get('khach_hang');
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = DonDatHang::with('user:id', 'traHang');
        // TH dat hang online 
        if (isset($don_hang) && $don_hang == 'dat_hang_online') {
            $query = $query->where('source_order', 'dat_hang_online');
        } elseif (isset($don_hang) && $don_hang == 'dat_hang_tai_quay') {
            // TH dat hang tai quay
            $query = $query->where('source_order', 'dat_hang_tai_quay');
        }

        // Tim kiem theo ngay
        if (isset($date) && count($date)) {
            $query = $query->whereBetween('order_date', [Carbon::parse($date[0])->addHours(7), Carbon::parse($date[1])->addHours(31)]);
        }

        if (isset($trangthai)) {
            $query = $query->where('trang_thai', $trangthai);
        }

        // Tinh tong hoa don/don dat hang
        return response()->json([
            'data' => $query->get(),
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
    public function downloadBaoCaoDatHang(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $trangThai = $request->get('don_hang');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        $allDonHang = DonDatHang::pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
            $allDonHang = DonDatHang::where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }
        $diemchay_data = SanPhamDonDatHang::whereIn('don_dat_hang_id', $allDonHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if ($trangThai == 'hoa_don') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if ($trangThai == 'don_dat_hang') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $diemchay_data =  $diemchay_data->orderBy('doanh_thu', 'desc')->get();
        } else {
            $diemchay_data =  $diemchay_data->orderBy($orderBy, 'desc')->get();
        }

        $diemchay_array[] = array('STT', 'Thời gian', 'Sản phẩm hàng hóa', 'Giá bán', 'Số lượng', 'Doanh thu');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Thời gian'  => $diemchay->created_at,
                'Sản phẩm hàng hóa'  => $diemchay->sanPham ? $diemchay->sanPham->ten_san_pham : "",
                'Giá bán' => $diemchay->sanPham ? $diemchay->gia_ban . ' /' . $diemchay->sanPham->don_vi_tinh : $diemchay->gia_ban,
                'Số lượng' => $diemchay->sanPham ? $diemchay->so_luong . " " . $diemchay->sanPham->don_vi_tinh :  $diemchay->so_luong,
                'Doanh thu' => $diemchay->doanh_thu ? $diemchay->doanh_thu : $diemchay->gia_ban * $diemchay->so_luong,
            );
        }
        \Excel::create('Báo cáo', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Báo cáo');
            $excel->sheet('Báo cáo', function ($sheet) use ($diemchay_array) {
                $sheet->fromArray($diemchay_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($diemchay_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));
                    if ($i == 0) {
                        foreach ($diemchay_array as $key => $diemchay) {
                            $sheet->getStyle($j . (++$key))->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );
                        }
                    }
                    $j++;
                }
            });
        })->download('xlsx');
    }

    public function getBaoCaoKhachHang(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }

        $sanPhamBanHang = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        $sanPhamDatHang = SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $sanPhamBanHang =  $sanPhamBanHang->orderBy('doanh_thu', 'desc');
            $sanPhamDatHang = $sanPhamDatHang->orderBy('doanh_thu', 'desc');
        } else {
            $sanPhamBanHang =  $sanPhamBanHang->orderBy($orderBy, 'desc');
            $sanPhamDatHang = $sanPhamDatHang->orderBy($orderBy, 'desc');
        }
        $sanPhamBanHang = $sanPhamBanHang->get();
        $sanPhamDatHang  = $sanPhamDatHang->get();
        return response(['ban_hang' => $sanPhamBanHang, 'dat_hang' => $sanPhamDatHang], 200);
    }
    public function downloadBaoCaoKhachHangHang(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $trangThai = $request->get('don_hang');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        $allDonHang = DonDatHang::pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
            $allDonHang = DonDatHang::where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }
        $diemchay_data = SanPhamDonDatHang::whereIn('don_dat_hang_id', $allDonHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if ($trangThai == 'hoa_don') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if ($trangThai == 'don_dat_hang') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $diemchay_data =  $diemchay_data->orderBy('doanh_thu', 'desc')->get();
        } else {
            $diemchay_data =  $diemchay_data->orderBy($orderBy, 'desc')->get();
        }

        $diemchay_array[] = array('STT', 'Thời gian', 'Sản phẩm hàng hóa', 'Giá bán', 'Số lượng', 'Doanh thu');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Thời gian'  => $diemchay->created_at,
                'Sản phẩm hàng hóa'  => $diemchay->sanPham ? $diemchay->sanPham->ten_san_pham : "",
                'Giá bán' => $diemchay->sanPham ? $diemchay->gia_ban . ' /' . $diemchay->sanPham->don_vi_tinh : $diemchay->gia_ban,
                'Số lượng' => $diemchay->sanPham ? $diemchay->so_luong . " " . $diemchay->sanPham->don_vi_tinh :  $diemchay->so_luong,
                'Doanh thu' => $diemchay->doanh_thu ? $diemchay->doanh_thu : $diemchay->gia_ban * $diemchay->so_luong,
            );
        }
        \Excel::create('Báo cáo', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Báo cáo');
            $excel->sheet('Báo cáo', function ($sheet) use ($diemchay_array) {
                $sheet->fromArray($diemchay_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($diemchay_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));
                    if ($i == 0) {
                        foreach ($diemchay_array as $key => $diemchay) {
                            $sheet->getStyle($j . (++$key))->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );
                        }
                    }
                    $j++;
                }
            });
        })->download('xlsx');
    }

    public function getBaoCaoCuoiNgay(Request $request)
    {
        $date = $request->get('date');
        $typeOrder = $request->get('don_hang');
        $orderBy = $request->get('orderBy');
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = DonDatHang::query();
        if (isset($typeOrder) && $typeOrder == 'hoa_don') {
            $query = $query->where('trang_thai', 'hoa_don');
        }
        if (isset($typeOrder) && $typeOrder == 'don_dat_hang') {
            $query = $query->where('trang_thai', 'moi_tao');
        }

        if (isset($date) && count($date)) {
            $query = $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        $tongTien = $query->sum('tong_tien');
        $data = $query->orderBy('created_at', "DESC")->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $data,
            'tong_tien' => $tongTien,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
    public function downloadBaoCaoCuoiNgay(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $trangThai = $request->get('don_hang');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        $allDonHang = DonDatHang::pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
            $allDonHang = DonDatHang::where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }
        $diemchay_data = SanPhamDonDatHang::whereIn('don_dat_hang_id', $allDonHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if ($trangThai == 'hoa_don') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if ($trangThai == 'don_dat_hang') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $diemchay_data =  $diemchay_data->orderBy('doanh_thu', 'desc')->get();
        } else {
            $diemchay_data =  $diemchay_data->orderBy($orderBy, 'desc')->get();
        }

        $diemchay_array[] = array('STT', 'Thời gian', 'Sản phẩm hàng hóa', 'Giá bán', 'Số lượng', 'Doanh thu');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Thời gian'  => $diemchay->created_at,
                'Sản phẩm hàng hóa'  => $diemchay->sanPham ? $diemchay->sanPham->ten_san_pham : "",
                'Giá bán' => $diemchay->sanPham ? $diemchay->gia_ban . ' /' . $diemchay->sanPham->don_vi_tinh : $diemchay->gia_ban,
                'Số lượng' => $diemchay->sanPham ? $diemchay->so_luong . " " . $diemchay->sanPham->don_vi_tinh :  $diemchay->so_luong,
                'Doanh thu' => $diemchay->doanh_thu ? $diemchay->doanh_thu : $diemchay->gia_ban * $diemchay->so_luong,
            );
        }
        \Excel::create('Báo cáo', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Báo cáo');
            $excel->sheet('Báo cáo', function ($sheet) use ($diemchay_array) {
                $sheet->fromArray($diemchay_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($diemchay_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));
                    if ($i == 0) {
                        foreach ($diemchay_array as $key => $diemchay) {
                            $sheet->getStyle($j . (++$key))->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );
                        }
                    }
                    $j++;
                }
            });
        })->download('xlsx');
    }

    public function getBaoCaoHangHoa(Request $request)
    {
        $date = $request->get('date');
        $danh_muc_id = $request->get('danh_muc_id');
        $perPage = $request->query('per_page', 10);
        $page = $request->get('page', 1);
        $query = SanPham::query();
        if (isset($danh_muc_id)) {
            $query = SanPham::where('danh_muc_id', $danh_muc_id);
        }
        $sanPhamID = $query->pluck('id')->toArray();
        $tongDoanhThu = SanPhamDonDatHang::whereIn('san_pham_id', $sanPhamID)->sum('doanh_thu');
        $tongDonHang =  SanPhamDonDatHang::whereIn('san_pham_id', $sanPhamID)->count();
        $data = $query->orderBy('updated_at', 'DESC')->paginate($perPage, ['*'], 'page', $page);
        $donHang = SanPhamDonDatHang::query();
        $traHang = DoiTraHang::query();
        if (isset($date) && count($date)) {
            $donHang = $donHang->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());

                $traHang = $traHang->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        foreach ($data as $item) {
            $soLuong = $donHang->where('san_pham_id', $item['id'])->sum('so_luong');
            $doanhThu =  $donHang->where('san_pham_id', $item['id'])->sum('doanh_thu');
            $soLuongTra = $traHang->where('san_pham_id', $item['id'])->sum('so_luong');
            $item['so_luong_ban'] = $soLuong;
            $item['doanh_thu'] = $doanhThu;
            $item['so_luong_tra'] = $soLuongTra;
        }
        return response()->json([
            'data' => $data,
            'doanh_thu' =>  $tongDoanhThu,
            'tong_don' => $tongDonHang,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
    public function downloadBaoCaoHangHoa(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $trangThai = $request->get('don_hang');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        $allDonHang = DonDatHang::pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
            $allDonHang = DonDatHang::where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }
        $diemchay_data = SanPhamDonDatHang::whereIn('don_dat_hang_id', $allDonHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if ($trangThai == 'hoa_don') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if ($trangThai == 'don_dat_hang') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $diemchay_data =  $diemchay_data->orderBy('doanh_thu', 'desc')->get();
        } else {
            $diemchay_data =  $diemchay_data->orderBy($orderBy, 'desc')->get();
        }

        $diemchay_array[] = array('STT', 'Thời gian', 'Sản phẩm hàng hóa', 'Giá bán', 'Số lượng', 'Doanh thu');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Thời gian'  => $diemchay->created_at,
                'Sản phẩm hàng hóa'  => $diemchay->sanPham ? $diemchay->sanPham->ten_san_pham : "",
                'Giá bán' => $diemchay->sanPham ? $diemchay->gia_ban . ' /' . $diemchay->sanPham->don_vi_tinh : $diemchay->gia_ban,
                'Số lượng' => $diemchay->sanPham ? $diemchay->so_luong . " " . $diemchay->sanPham->don_vi_tinh :  $diemchay->so_luong,
                'Doanh thu' => $diemchay->doanh_thu ? $diemchay->doanh_thu : $diemchay->gia_ban * $diemchay->so_luong,
            );
        }
        \Excel::create('Báo cáo', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Báo cáo');
            $excel->sheet('Báo cáo', function ($sheet) use ($diemchay_array) {
                $sheet->fromArray($diemchay_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($diemchay_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));
                    if ($i == 0) {
                        foreach ($diemchay_array as $key => $diemchay) {
                            $sheet->getStyle($j . (++$key))->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );
                        }
                    }
                    $j++;
                }
            });
        })->download('xlsx');
    }

    public function getBaoCaoNhaCungCap(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }

        $sanPhamBanHang = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        $sanPhamDatHang = SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $sanPhamBanHang =  $sanPhamBanHang->orderBy('doanh_thu', 'desc');
            $sanPhamDatHang = $sanPhamDatHang->orderBy('doanh_thu', 'desc');
        } else {
            $sanPhamBanHang =  $sanPhamBanHang->orderBy($orderBy, 'desc');
            $sanPhamDatHang = $sanPhamDatHang->orderBy($orderBy, 'desc');
        }
        $sanPhamBanHang = $sanPhamBanHang->get();
        $sanPhamDatHang  = $sanPhamDatHang->get();
        return response(['ban_hang' => $sanPhamBanHang, 'dat_hang' => $sanPhamDatHang], 200);
    }
    public function downloadBaoCaoNhaCungCap(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $trangThai = $request->get('don_hang');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        $allDonHang = DonDatHang::pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
            $allDonHang = DonDatHang::where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }
        $diemchay_data = SanPhamDonDatHang::whereIn('don_dat_hang_id', $allDonHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if ($trangThai == 'hoa_don') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if ($trangThai == 'don_dat_hang') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $diemchay_data =  $diemchay_data->orderBy('doanh_thu', 'desc')->get();
        } else {
            $diemchay_data =  $diemchay_data->orderBy($orderBy, 'desc')->get();
        }

        $diemchay_array[] = array('STT', 'Thời gian', 'Sản phẩm hàng hóa', 'Giá bán', 'Số lượng', 'Doanh thu');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Thời gian'  => $diemchay->created_at,
                'Sản phẩm hàng hóa'  => $diemchay->sanPham ? $diemchay->sanPham->ten_san_pham : "",
                'Giá bán' => $diemchay->sanPham ? $diemchay->gia_ban . ' /' . $diemchay->sanPham->don_vi_tinh : $diemchay->gia_ban,
                'Số lượng' => $diemchay->sanPham ? $diemchay->so_luong . " " . $diemchay->sanPham->don_vi_tinh :  $diemchay->so_luong,
                'Doanh thu' => $diemchay->doanh_thu ? $diemchay->doanh_thu : $diemchay->gia_ban * $diemchay->so_luong,
            );
        }
        \Excel::create('Báo cáo', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Báo cáo');
            $excel->sheet('Báo cáo', function ($sheet) use ($diemchay_array) {
                $sheet->fromArray($diemchay_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($diemchay_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));
                    if ($i == 0) {
                        foreach ($diemchay_array as $key => $diemchay) {
                            $sheet->getStyle($j . (++$key))->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );
                        }
                    }
                    $j++;
                }
            });
        })->download('xlsx');
    }

    public function getBaoCaoNhanVien(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }

        $sanPhamBanHang = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        $sanPhamDatHang = SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $sanPhamBanHang =  $sanPhamBanHang->orderBy('doanh_thu', 'desc');
            $sanPhamDatHang = $sanPhamDatHang->orderBy('doanh_thu', 'desc');
        } else {
            $sanPhamBanHang =  $sanPhamBanHang->orderBy($orderBy, 'desc');
            $sanPhamDatHang = $sanPhamDatHang->orderBy($orderBy, 'desc');
        }
        $sanPhamBanHang = $sanPhamBanHang->get();
        $sanPhamDatHang  = $sanPhamDatHang->get();
        return response(['ban_hang' => $sanPhamBanHang, 'dat_hang' => $sanPhamDatHang], 200);
    }
    public function downloadBaoCaoNhanVien(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $trangThai = $request->get('don_hang');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        $allDonHang = DonDatHang::pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
            $allDonHang = DonDatHang::where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }
        $diemchay_data = SanPhamDonDatHang::whereIn('don_dat_hang_id', $allDonHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if ($trangThai == 'hoa_don') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if ($trangThai == 'don_dat_hang') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $diemchay_data =  $diemchay_data->orderBy('doanh_thu', 'desc')->get();
        } else {
            $diemchay_data =  $diemchay_data->orderBy($orderBy, 'desc')->get();
        }

        $diemchay_array[] = array('STT', 'Thời gian', 'Sản phẩm hàng hóa', 'Giá bán', 'Số lượng', 'Doanh thu');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Thời gian'  => $diemchay->created_at,
                'Sản phẩm hàng hóa'  => $diemchay->sanPham ? $diemchay->sanPham->ten_san_pham : "",
                'Giá bán' => $diemchay->sanPham ? $diemchay->gia_ban . ' /' . $diemchay->sanPham->don_vi_tinh : $diemchay->gia_ban,
                'Số lượng' => $diemchay->sanPham ? $diemchay->so_luong . " " . $diemchay->sanPham->don_vi_tinh :  $diemchay->so_luong,
                'Doanh thu' => $diemchay->doanh_thu ? $diemchay->doanh_thu : $diemchay->gia_ban * $diemchay->so_luong,
            );
        }
        \Excel::create('Báo cáo', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Báo cáo');
            $excel->sheet('Báo cáo', function ($sheet) use ($diemchay_array) {
                $sheet->fromArray($diemchay_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($diemchay_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));
                    if ($i == 0) {
                        foreach ($diemchay_array as $key => $diemchay) {
                            $sheet->getStyle($j . (++$key))->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );
                        }
                    }
                    $j++;
                }
            });
        })->download('xlsx');
    }

    public function getBaoCaoTaiChinh(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }

        $sanPhamBanHang = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        $sanPhamDatHang = SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $sanPhamBanHang =  $sanPhamBanHang->orderBy('doanh_thu', 'desc');
            $sanPhamDatHang = $sanPhamDatHang->orderBy('doanh_thu', 'desc');
        } else {
            $sanPhamBanHang =  $sanPhamBanHang->orderBy($orderBy, 'desc');
            $sanPhamDatHang = $sanPhamDatHang->orderBy($orderBy, 'desc');
        }
        $sanPhamBanHang = $sanPhamBanHang->get();
        $sanPhamDatHang  = $sanPhamDatHang->get();
        return response(['ban_hang' => $sanPhamBanHang, 'dat_hang' => $sanPhamDatHang], 200);
    }
    public function downloadBaoCaoTaiChinh(Request $request)
    {
        $date = $request->get('date');
        $orderBy = $request->get('orderBy');
        $trangThai = $request->get('don_hang');
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        $allDonHang = DonDatHang::pluck('id')->toArray();
        if (isset($date) && count($date)) {
            $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();

            $datHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
            $allDonHang = DonDatHang::where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay())->pluck('id')->toArray();
        }
        $diemchay_data = SanPhamDonDatHang::whereIn('don_dat_hang_id', $allDonHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        if ($trangThai == 'hoa_don') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if ($trangThai == 'don_dat_hang') {
            $diemchay_data =  SanPhamDonDatHang::whereIn('don_dat_hang_id', $datHang)->with(['sanPham:id,ten_san_pham,don_vi_tinh']);
        }
        if (!isset($orderBy) || ($orderBy != 'doanh_thu' && $orderBy != 'so_luong')) {
            $diemchay_data =  $diemchay_data->orderBy('doanh_thu', 'desc')->get();
        } else {
            $diemchay_data =  $diemchay_data->orderBy($orderBy, 'desc')->get();
        }

        $diemchay_array[] = array('STT', 'Thời gian', 'Sản phẩm hàng hóa', 'Giá bán', 'Số lượng', 'Doanh thu');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Thời gian'  => $diemchay->created_at,
                'Sản phẩm hàng hóa'  => $diemchay->sanPham ? $diemchay->sanPham->ten_san_pham : "",
                'Giá bán' => $diemchay->sanPham ? $diemchay->gia_ban . ' /' . $diemchay->sanPham->don_vi_tinh : $diemchay->gia_ban,
                'Số lượng' => $diemchay->sanPham ? $diemchay->so_luong . " " . $diemchay->sanPham->don_vi_tinh :  $diemchay->so_luong,
                'Doanh thu' => $diemchay->doanh_thu ? $diemchay->doanh_thu : $diemchay->gia_ban * $diemchay->so_luong,
            );
        }
        \Excel::create('Báo cáo', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Báo cáo');
            $excel->sheet('Báo cáo', function ($sheet) use ($diemchay_array) {
                $sheet->fromArray($diemchay_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($diemchay_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));
                    if ($i == 0) {
                        foreach ($diemchay_array as $key => $diemchay) {
                            $sheet->getStyle($j . (++$key))->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );
                        }
                    }
                    $j++;
                }
            });
        })->download('xlsx');
    }
}
