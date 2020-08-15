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
        if($date){
            $date[0] = Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay();
            $date[1] = Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay();
        }else{
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
        foreach($sanPhams as $item){
            $query = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->where('created_at', '>=', $date[0])->where('created_at', '<=', $date[1]);
            $doanhThu = 0;
            if($type == 'doanh_thu'){
                $doanhThu = $query->where('san_pham_id', $item->san_pham_id)->sum('doanh_thu');
            }
            if($type == 'so_luong'){
                $doanhThu = $query->where('san_pham_id', $item->san_pham_id)->sum('so_luong');
            }
            
            $item['tong_doanh_thu'] = $doanhThu;
        };
        $sanPhams =  collect($sanPhams)->sortByDesc('tong_doanh_thu')->values()->take(8);
        return $sanPhams;
    }

    public function getDoanhThu(){
        $year = Carbon::now()->year;
        $data = [];
        for($i = 1; $i<=12; $i++){
            $hoaDon = DonDatHang::whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $i)->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
            $doanhThu = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon)->sum('doanh_thu');
            $data[] = $doanhThu;
        }
        return $data;
    }

    public function getThongTinDashBoard(){
        $khachHang = KhachHang::query()->count();
        $sanPham = SanPham::query()->count();
        $donDatHang = DonDatHang::query()->count();
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->whereYear('created_at', '=', Carbon::now()->year)->whereMonth('created_at', '=', Carbon::now()->month)->count();
        $hoaDons = DonDatHang::whereYear('created_at', '=', Carbon::now()->year)->whereMonth('created_at', '=', Carbon::now()->month)->where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $doanhThu = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDons)->sum('doanh_thu');
        $data = [
            'khach_hang' => $khachHang,
            'san_pham' => $sanPham,
            'don_hang' => $donDatHang,
            'hoa_don' => $hoaDon,
            'doanh_thu' => $doanhThu
        ];
        return $data;
    }
}
