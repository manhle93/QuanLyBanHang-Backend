<?php

namespace App\Http\Controllers;

use App\DonDatHang;
use App\KhachHang;
use App\SanPham;
use App\SanPhamDonDatHang;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BieuDoController extends Controller
{
    public function getSanPhamBanChay(Request $request)
    {
        $date = $request->get('date');
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $type = $request->get('type');
        if ($date) {
            $date[0] = Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay();
            $date[1] = Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay();
        } else {
            $date = array();
            $date[] = Carbon::now()->firstOfMonth()->startOfDay();
            $date[] = Carbon::now()->endOfMonth()->endOfDay();
        }
        // $sanPhamID = SanPhamDonDatHang::where('created_at', '>=', $date[0])->where('created_at', '<=', $date[1])->get();
        $hoaDon = DonDatHang::where('created_at', '>=',  $date[0])->where('created_at', '<=', $date[1])->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        // $sanPhamID =  collect($sanPhamID);
        // $sanPhamID = $sanPhamID->unique('san_pham_id')->pluck('san_pham_id')->toArray();
        $sanPhams = SanPhamDonDatHang::with('sanPham:id,ten_san_pham')->select('id', 'san_pham_id', 'doanh_thu')->whereIn('don_dat_hang_id', $hoaDon)->where('created_at', '>=', $date[0])->whereMonth('created_at', '<=', $date[1])->get();
        $sanPhams =  collect($sanPhams)->unique('san_pham_id')->values()->all();
        foreach ($sanPhams as $item) {
            $query = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->where('created_at', '>=', $date[0])->where('created_at', '<=', $date[1]);
            $doanhThu = 0;
            if ($type == 'doanh_thu') {
                $doanhThu = $query->where('san_pham_id', $item->san_pham_id)->sum('doanh_thu');
            }
            if ($type == 'so_luong') {
                $doanhThu = $query->where('san_pham_id', $item->san_pham_id)->sum('so_luong');
            }

            $item['tong_doanh_thu'] = $doanhThu;
        };
        $sanPhams =  collect($sanPhams)->sortByDesc('tong_doanh_thu')->values()->take(8);
        return $sanPhams;
    }

    public function getDoanhThu(Request $request)
    {
        $type =  $request->type;
        $data = [];
        $time = [];
        switch ($type) {
            case 'hom_nay':
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $hoaDon = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->where('trang_thai', 'hoa_don')->get();
                foreach ($hoaDon as $item) {
                    $doanhThu = SanPhamDonDatHang::where('don_dat_hang_id', $item->id)->sum('doanh_thu');
                    $data[] = $doanhThu;
                    $time[] = Carbon::parse($item->created_at)->format('d/M/Y h:i');
                }

                break;
            case 'hom_qua':
                $now = Carbon::yesterday()->timezone('Asia/Ho_Chi_Minh');
                $hoaDon = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->where('trang_thai', 'hoa_don')->get();
                foreach ($hoaDon as $item) {
                    $doanhThu = SanPhamDonDatHang::where('don_dat_hang_id', $item->id)->sum('doanh_thu');
                    $data[] = $doanhThu;
                    $time[] = Carbon::parse($item->created_at)->format('d/M/Y h:i');
                }

                break;

            case 'bay_ngay_truoc':
                for ($i = 1; $i <= 7; $i++) {
                    $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                    $hoaDon = DonDatHang::where('created_at', '>=', Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->startOfDay())->where('created_at', '<=', Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->endOfDay())->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
                    $doanhThu = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->sum('doanh_thu');
                    $data[] = $doanhThu;
                    $time[] =  $now->copy()->subDays($i)->format('d/M/Y');
                }
                break;

            case 'tuan_nay':
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $max = $now->diffInDays(Carbon::now()->timezone('Asia/Ho_Chi_Minh')->startOfWeek());

                for ($i = $max - 1; $i >= 0; $i--) {
                    $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                    $hoaDon = DonDatHang::where('created_at', '>=', Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->startOfDay())->where('created_at', '<=', Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->endOfDay())->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
                    $doanhThu = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->sum('doanh_thu');
                    $data[] = $doanhThu;
                    $time[] =  Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->format('d/M/Y');
                }
                break;

            case 'thang_nay':
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $max = $now->diffInDays(new Carbon('first day of this month'));

                for ($i = $max - 1; $i >= 0; $i--) {
                    $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                    $hoaDon = DonDatHang::where('created_at', '>=', Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->startOfDay())->where('created_at', '<=', Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->endOfDay())->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
                    $doanhThu = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->sum('doanh_thu');
                    $data[] = $doanhThu;
                    $time[] =  Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->format('d/M/Y');
                }
                break;
            case 'thang_truoc':
                $startMonth = new Carbon('first day of last month');
                $endMonth = new Carbon('last day of last month');
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $max = $now->diffInDays($startMonth);
                $min = $now->diffInDays($endMonth);

                for ($i = $max - 1; $i >= $min; $i--) {
                    $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                    $hoaDon = DonDatHang::where('created_at', '>=', Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->startOfDay())->where('created_at', '<=', Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->endOfDay())->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
                    $doanhThu = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->sum('doanh_thu');
                    $data[] = $doanhThu;
                    $time[] =  Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($i)->format('d/M/Y');
                }
                break;

            case 'nam_nay':
                $year = Carbon::now()->year;
                for ($i = 1; $i <= 12; $i++) {
                    $hoaDon = DonDatHang::whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $i)->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
                    $doanhThu = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->sum('doanh_thu');
                    $data[] = $doanhThu;
                    $time[] = 'Tháng ' . $i;
                }
                break;
            case 'nam_truoc':
                $year = Carbon::now()->year;
                $year = $year - 1;
                for ($i = 1; $i <= 12; $i++) {
                    $hoaDon = DonDatHang::whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $i)->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
                    $doanhThu = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->sum('doanh_thu');
                    $data[] = $doanhThu;
                    $time[] = 'Tháng ' . $i;
                }
                break;
        };
        return response(['data' => $data, 'time' => $time]);
    }

    public function getThongTinDashBoard(Request $request)
    {
        $type =  $request->type;
        $khachHang = 0;
        $sanPham = 0;
        $donDatHang = 0;
        $hoaDon = 0;
        $doanhThu = 0;
        $donOnline = 0;
        switch ($type) {
            case 'hom_nay':
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->count();
                $hoaDons = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->pluck('id')->toArray();
                $doanhThu = DonDatHang::whereIn('id', $hoaDons)->sum('da_thanh_toan') +  DonDatHang::whereIn('id', $hoaDons)->sum('con_phai_thanh_toan');
                $donOnline = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->where('trang_thai', 'hoa_don')->count();
                $donDatHang = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->count();
                break;
            case 'hom_qua':
                $now = Carbon::yesterday()->timezone('Asia/Ho_Chi_Minh');
                $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->count();
                $hoaDons = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->pluck('id')->toArray();
                $doanhThu = DonDatHang::whereIn('id', $hoaDons)->sum('da_thanh_toan') +  DonDatHang::whereIn('id', $hoaDons)->sum('con_phai_thanh_toan');
                $donOnline = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->where('trang_thai', 'hoa_don')->count();
                $donDatHang = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', $now->copy()->endOfDay())->count();
                break;

            case 'bay_ngay_truoc':
                $now = Carbon::now()->subDays(7)->timezone('Asia/Ho_Chi_Minh');
                $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<',  Carbon::now()->endOfDay())->count();
                $hoaDons = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<',  Carbon::now())->pluck('id')->toArray();
                $doanhThu = DonDatHang::whereIn('id', $hoaDons)->sum('da_thanh_toan') +  DonDatHang::whereIn('id', $hoaDons)->sum('con_phai_thanh_toan');
                $donOnline = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<',  Carbon::now())->where('trang_thai', 'hoa_don')->count();
                $donDatHang = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', Carbon::now()->endOfDay())->count();
                break;

            case 'tuan_nay':
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $now = $now->startOfWeek();
                // $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->subDays($day);
                $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<',  Carbon::now()->endOfDay())->count();
                $hoaDons = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<',  Carbon::now())->pluck('id')->toArray();
                $doanhThu = DonDatHang::whereIn('id', $hoaDons)->sum('da_thanh_toan') +  DonDatHang::whereIn('id', $hoaDons)->sum('con_phai_thanh_toan');
                $donOnline = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<',  Carbon::now())->where('trang_thai', 'hoa_don')->count();
                $donDatHang = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', Carbon::now()->endOfDay())->count();
                break;

            case 'thang_nay':
                $now = new Carbon('first day of this month');
                $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<',  Carbon::now()->endOfDay())->count();
                $hoaDons = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfDay())->where('created_at', '<',  Carbon::now()->endOfDay())->pluck('id')->toArray();
                $doanhThu = DonDatHang::whereIn('id', $hoaDons)->sum('da_thanh_toan') +  DonDatHang::whereIn('id', $hoaDons)->sum('con_phai_thanh_toan');
                $donOnline = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<',  Carbon::now()->endOfDay())->where('trang_thai', 'hoa_don')->count();
                $donDatHang = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', Carbon::now()->endOfDay())->count();
                break;

            case 'thang_truoc':
                $startMonth = new Carbon('first day of last month');
                $endMonth = new Carbon('last day of last month');

                $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $startMonth->startOfDay())->where('created_at', '<',  $endMonth->endOfDay())->count();
                $hoaDons = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $startMonth->startOfDay())->where('created_at', '<', $endMonth->endOfDay())->pluck('id')->toArray();
                $doanhThu = DonDatHang::whereIn('id', $hoaDons)->sum('da_thanh_toan') +  DonDatHang::whereIn('id', $hoaDons)->sum('con_phai_thanh_toan');
                $donOnline = DonDatHang::where('created_at', '>=', $startMonth->startOfDay())->where('created_at', '<',  $endMonth->endOfDay())->where('trang_thai', 'hoa_don')->count();
                $donDatHang = DonDatHang::where('created_at', '>=', $startMonth->startOfDay())->where('created_at', '<=', $endMonth->endOfDay())->count();
                break;

            case 'nam_nay':
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfYear())->where('created_at', '<',  Carbon::now()->endOfYear())->count();
                $hoaDons = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $now->startOfYear())->where('created_at', '<',  Carbon::now()->endOfYear())->pluck('id')->toArray();
                $doanhThu = DonDatHang::whereIn('id', $hoaDons)->sum('da_thanh_toan') +  DonDatHang::whereIn('id', $hoaDons)->sum('con_phai_thanh_toan');
                $donOnline = DonDatHang::where('created_at', '>=', $now->startOfYear())->where('created_at', '<',  Carbon::now()->endOfYear())->where('trang_thai', 'hoa_don')->count();
                $donDatHang = DonDatHang::where('created_at', '>=', $now->startOfDay())->where('created_at', '<=', Carbon::now()->endOfYear())->count();
                break;



            case 'nam_truoc':
                $startYear = new Carbon('first day of last year');
                $endYear = $startYear->copy()->endOfYear();
                $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $startYear->startOfDay())->where('created_at', '<',  $endYear->endOfDay())->count();
                $hoaDons = DonDatHang::where('trang_thai', 'hoa_don')->where('created_at', '>=', $startYear->startOfDay())->where('created_at', '<', $endYear->endOfDay())->pluck('id')->toArray();
                $doanhThu = DonDatHang::whereIn('id', $hoaDons)->sum('da_thanh_toan') +  DonDatHang::whereIn('id', $hoaDons)->sum('con_phai_thanh_toan');
                $donOnline = DonDatHang::where('created_at', '>=', $startYear->startOfDay())->where('created_at', '<',  $endYear->endOfDay())->where('trang_thai', 'hoa_don')->count();
                $donDatHang = DonDatHang::where('created_at', '>=', $startYear->startOfDay())->where('created_at', '<=', $endYear->endOfDay())->count();
                break;
        };

        // $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->whereYear('created_at', '=', Carbon::now()->year)->whereMonth('created_at', '=', Carbon::now()->month)->count();
        // $hoaDons = DonDatHang::whereYear('created_at', '=', Carbon::now()->year)->whereMonth('created_at', '=', Carbon::now()->month)->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        // $doanhThu = DonDatHang::whereIn('id', $hoaDons)->sum('da_thanh_toan') +  DonDatHang::whereIn('id', $hoaDons)->sum('con_phai_thanh_toan');
        // $donOnline = DonDatHang::where('trang_thai', 'mua_hang_online')->whereYear('created_at', '=', Carbon::now()->year)->whereMonth('created_at', '=', Carbon::now()->month)->count();

        $khachHang = KhachHang::query()->count();
        $sanPham = SanPham::query()->count();

        $data = [
            'khach_hang' => $khachHang,
            'san_pham' => $sanPham,
            'don_hang' => $donDatHang,
            'hoa_don' => $hoaDon,
            'doanh_thu' => $doanhThu,
            'don_online' => $donOnline,

        ];
        return $data;
    }
}
