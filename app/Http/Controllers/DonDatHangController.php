<?php

namespace App\Http\Controllers;

use App\DonDatHang;
use App\SanPhamDonDatHang;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
// use DB;
use Illuminate\Support\Facades\DB;

class DonDatHangController extends Controller
{
    public function addDonDatHang(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'ma' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Thiếu dữ liệu, không thể đặt hàng'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        if ($data['trang_thai'] == 'hoa_don') {
            $data['da_thanh_toan'] = $data['tong_tien'] -  $data['giam_gia'];
            $data['con_phai_thanh_toan'] = 0;
        }
        try {
            DB::beginTransaction();
            $donHang = DonDatHang::create([
                'ma' => $data['ma'],
                'tong_tien' => $data['tong_tien'],
                'ten' => $data['ten'],
                'user_id' => $data['khach_hang_id'],
                'ghi_chu' => $data['ghi_chu'],
                'giam_gia' => $data['giam_gia'],
                'bang_gia_id' => $data['bang_gia_id'],
                'da_thanh_toan' => $data['da_thanh_toan'],
                'trang_thai' => $data['trang_thai'],
                'con_phai_thanh_toan' => $data['con_phai_thanh_toan'],

            ]);
            foreach ($data['danhSachHang'] as $item) {
                SanPhamDonDatHang::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'gia_ban' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_dat_hang_id' => $donHang->id
                ]);
            }
            DB::commit();
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể đặt hàng'], 500);
        }
    }

    public function getDonHang(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = DonDatHang::with('user', 'sanPhams', 'sanPhams.sanPham:id,ten_san_pham,don_vi_tinh');
        $donHang = [];
        if ($user->role_id == 1 || $user->role_id == 2) {
            $donHang = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        // if ($user->role_id == 3) {
        //     $donHang = $query->where('user_id', $user->id)->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        // }
        return response()->json([
            'data' => $donHang,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function xoaDonHang($id)
    {
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            $donHang = DonDatHang::where('id', $id)->first();
            if ($donHang->trang_thai == 'hoa_don') {
                return response(['message' => 'Không thể xóa đơn đặt hàng đã chuyển hóa đơn'], 500);
            }
            $donHang->delete();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa đơn đặt hàng này'], 500);
        }
    }

    public function getChiTietDonDatHang($id)
    {
        $donHang = DonDatHang::with('sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        return response(['data' => $donHang], 200);
    }

    public function updateDonDatHang($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'ma' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Thiếu dữ liệu, không thể đặt hàng'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        if ($data['trang_thai'] == 'hoa_don') {
            $data['da_thanh_toan'] = $data['tong_tien'] -  $data['giam_gia'];
            $data['con_phai_thanh_toan'] = 0;
        }
        try {
            DB::beginTransaction();
            $donHang = DonDatHang::where('id', $id)->first()->update([
                'ma' => $data['ma'],
                'tong_tien' => $data['tong_tien'],
                'ten' => $data['ten'],
                'user_id' => $data['khach_hang_id'],
                'ghi_chu' => $data['ghi_chu'],
                'giam_gia' => $data['giam_gia'],
                'da_thanh_toan' => $data['da_thanh_toan'],
                'trang_thai' => $data['trang_thai'],
                'con_phai_thanh_toan' => $data['con_phai_thanh_toan'],
                'bang_gia_id' => $data['bang_gia_id'],

            ]);
            SanPhamDonDatHang::where('don_dat_hang_id', $id)->delete();
            foreach ($data['danhSachHang'] as $item) {
                SanPhamDonDatHang::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'gia_ban' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_dat_hang_id' => $id
                ]);
            }
            DB::commit();
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật'], 500);
        }
    }
    public function huyDon($id)
    {
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            DonDatHang::where('id', $id)->first()->update(['trang_thai' => 'huy_bo']);
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể hủy đơn'], 500);
        }
    }
    public function chuyenHoaDon($id, Request $request)
    {
        $user = auth()->user();
        $shipper_id = $request->get('nhan_vien_giao_hang');
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            $donHang = DonDatHang::where('id', $id)->first();
            $donHang->update([
                'trang_thai' => 'hoa_don',
                'nhan_vien_giao_hang' => $shipper_id,
                'da_thanh_toan' => $donHang->tong_tien - $donHang->giam_gia,
                'con_phai_thanh_toan' => 0
            ]);
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể chuyển hóa đơn'], 500);
        }
    }
}
