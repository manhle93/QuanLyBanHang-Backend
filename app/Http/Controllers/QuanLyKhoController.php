<?php

namespace App\Http\Controllers;

use App\DonDatHang;
use App\DonHangNhaCungCap;
use App\HangTonKho;
use App\PhieuNhapKho;
use App\SanPham;
use App\SanPhamDonDatHang;
use App\SanPhamDonHangNhaCungCap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuanLyKhoController extends Controller
{
    public function getPhieuNhap(Request $request)
    {
        $user = auth()->user();
        $date = $request->get('date');
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập'], 401);
        }
        if ($user->role_id != 1 && $user->role_id != 2) {
            return response(['message' => 'Không có quyền'], 402);
        }
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $query = PhieuNhapKho::with('donHang', 'donHang.sanPhams', 'donHang.sanPhams.sanPham:id,ten_san_pham');
        if (isset($search)) {
            $search = trim($search);
            $query->where(function($query) use ($search){
                $query->where('ma', 'ilike', "%{$search}%")
                ->orWhereHas('donHang', function($query) use ($search){
                    $query->where('ma', 'ilike', "%{$search}%")
                    ->orWhere('ten', 'ilike', "%{$search}%");
                });
            });
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        $query->orderBy('created_at', 'desc');
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function hangTonKho()
    {
        $sanPham = SanPham::select('id', 'ten_san_pham')->get();
        $donHangNhapKho = DonHangNhaCungCap::where('trang_thai', 'nhap_kho')->pluck('id')->toArray();
        foreach ($sanPham as $item) {
            $soNhapKho = SanPhamDonHangNhaCungCap::where('san_pham_id', $item->id)->whereIn('don_hang_id', $donHangNhapKho)->sum('so_luong');
            $item['ton_kho'] = $soNhapKho;
        }
        return $sanPham;
    }

    public function getHangTonKho(Request $request)
    {

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = HangTonKho::with('sanPham', 'kho');
        $search = $request->get('search');
        $danh_muc_id = $request->get('danh_muc_id');
        $hoaDons = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        if (isset($danh_muc_id)) {
            $query->whereHas('sanPham', function ($query) use ($danh_muc_id) {
                $query->where('danh_muc_id', $danh_muc_id);
            });
        }
        if (isset($search)) {
            $search = trim($search);
            $query->whereHas('sanPham', function ($query) use ($search) {
                $query->where('ten_san_pham', 'ilike', "%{$search}%");
                $query->orWhere('mo_ta_san_pham', 'ilike', "%{$search}%");
            });
        }

        $query->orderBy('updated_at', 'desc');
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        foreach ($data as $item) {
            $soLuong = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDons)->where('san_pham_id', $item['san_pham_id'])->sum('so_luong');
            $item['da_ban'] =  $soLuong;
        }

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function addNhapKhoNgoai(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'hangHoas' => 'required',
            'tong_tien' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm mới'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        DB::beginTransaction();
        $user = auth()->user();
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập'], 402);
        }
        try {
            $donHang = DonHangNhaCungCap::create([
                'ma' => 'ĐHN' . time(),
                'ten' => 'Đơn hàng mua ngoài',
                'ghi_chu' => 'Mua hàng bên ngoài, nhập kho',
                'chiet_khau' => 0,
                'tong_tien' => $data['tong_tien'],
                'thoi_gian' => Carbon::now(),
                'user_id' => null,
                'trang_thai' => 'nhap_kho_ngoai'
            ]);
            foreach ($data['hangHoas'] as $item) {
                SanPhamDonHangNhaCungCap::create([
                    'san_pham_id' => $item['san_pham_id'],
                    'don_gia' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_hang_id' => $donHang->id
                ]);
                $checkKho = HangTonKho::where('san_pham_id', $item['san_pham_id'])->first();
                if ($checkKho) {
                    $checkKho->update(['so_luong' => $checkKho->so_luong + $item['so_luong']]);
                } else {
                    HangTonKho::create(['san_pham_id' => $item['san_pham_id'], 'so_luong' => $item['so_luong']]);
                }
            }
            PhieuNhapKho::create(['don_hang_id' => $donHang->id, 'ma' => 'PNK' . $donHang->id, 'user_id' => $user->id, 'kho_id' => null]);
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể nhập kho'], 500);
        }
    }
}
