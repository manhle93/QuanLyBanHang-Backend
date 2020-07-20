<?php

namespace App\Http\Controllers;

use App\DonHangNhaCungCap;
use App\HangTonKho;
use App\Kho;
use App\PhieuNhapKho;
use App\SanPhamDonHangNhaCungCap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

use Exception;

class DonHangNhaCungCapController extends Controller
{
    public function getDonHang(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $date = $request->get('date');
        $nhac_cung_cap = $request->get('nha_cung_cap');
        $query = DonHangNhaCungCap::with('user', 'sanPhams');
        $donHang = [];
        if (isset($nhac_cung_cap)) {
            $query = $query->where('user_id', $nhac_cung_cap);
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if ($user->role_id == 1 || $user->role_id == 2) {
            $donHang = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        if ($user->role_id == 3) {
            $donHang = $query->where('user_id', $user->id)->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        return response()->json([
            'data' => $donHang,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function addDonHang(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'thoi_gian' => 'required',
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
        $user = auth()->user();
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập'], 500);
        }
        if($user->role_id == 3){
            $data['nha_cung_cap_id'] = null;
        }
        if($user->role_id != 3 && $user->role_id != 2 && $user->role_id != 1){
            return response(['message' => 'Không có quyền'], 4001);
        }
        try {
            DB::beginTransaction();
            $data['thoi_gian'] = Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
            $donHang = DonHangNhaCungCap::create([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'ghi_chu' => $data['ghi_chu'],
                'chiet_khau' => $data['chiet_khau'],
                'tong_tien' => $data['tong_tien'],
                'thoi_gian' => $data['thoi_gian'],
                'user_id' => $data['nha_cung_cap_id'] ? $data['nha_cung_cap_id'] : $user->id,
                'trang_thai' => 'moi_tao'
            ]);
            foreach ($data['danhSachHang'] as $item) {
                SanPhamDonHangNhaCungCap::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'don_gia' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_hang_id' => $donHang->id
                ]);
            }
            DB::commit();
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể thêm mới'], 500);
        }
    }
    public function getChiTietDonHang($id)
    {
        $donHang = DonHangNhaCungCap::with('sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        return response(['data' => $donHang], 200);
    }
    public function update($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'thoi_gian' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể cập nhật'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $user = auth()->user();
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập'], 500);
        }
        if($user->role_id == 3){
            $data['nha_cung_cap_id'] = null;
        }
        if($user->role_id != 3 && $user->role_id != 2 && $user->role_id != 1){
            return response(['message' => 'Không có quyền'], 4001);
        }
        try {
            DB::beginTransaction();
            $data['thoi_gian'] = Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
            $donHang = DonHangNhaCungCap::where('id', $id)->first()->update([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'ghi_chu' => $data['ghi_chu'],
                'chiet_khau' => $data['chiet_khau'],
                'tong_tien' => $data['tong_tien'],
                'thoi_gian' => $data['thoi_gian'],
                'user_id' => $data['nha_cung_cap_id'] ? $data['nha_cung_cap_id'] : $user->id,
                'trang_thai' => 'moi_tao'
            ]);
            SanPhamDonHangNhaCungCap::where('don_hang_id', $id)->delete();
            foreach ($data['danhSachHang'] as $item) {
                SanPhamDonHangNhaCungCap::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'don_gia' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_hang_id' => $id
                ]);
            }
            DB::commit();
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể cập nhật'], 500);
        }
    }

    public function duyetDon($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }
        $donHang = DonHangNhaCungCap::where('id', $id)->first();
        if ($user->role_id == 1 || $user->id == $donHang->id) {
            try {
                DonHangNhaCungCap::find($id)->update(['trang_thai' => 'da_duyet']);
                return response(['message' => "Duyệt đơn thành công"], 200);
            } catch (\Exception $e) {
                return response(['message' => "Không thể duyệt đơn"], 500);
            }
        } else  return response(['message' => "Không có quyền duyệt đơn"], 401);
    }

    public function huyDon($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }
        $donHang = DonHangNhaCungCap::where('id', $id)->first();
        if ($user->role_id == 1 || $user->id == $donHang->id) {
            try {
                DonHangNhaCungCap::find($id)->update(['trang_thai' => 'huy_bo']);
                return response(['message' => "Duyệt đơn thành công"], 200);
            } catch (\Exception $e) {
                return response(['message' => "Không thể duyệt đơn"], 500);
            }
        } else  return response(['message' => "Không có quyền hủy đơn"], 401);
    }

    public function xoaDon($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }
        $donHang = DonHangNhaCungCap::where('id', $id)->first();

        if ($user->role_id == 1 || $donHang->user_id == $user->id) {
            try {
                if($donHang->trang_thai == 'nhap_kho'){
                    return response(['message' => "Không thể xóa đơn hàng đã nhập kho"], 500);
                }
                $donHang->delete();
                return response(['message' => "Xóa đơn thành công"], 200);
            } catch (\Exception $e) {
                return response(['message' => "Không thể xóa đơn hàng"], 500);
            }
        } else return response(['message' => "Không có quyền xóa đơn"], 401);
    }

    public function nhapKho($id, Request $request)
    {
        $user = auth()->user();
        $kho_id = $request->kho_id;
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }
        if (!$kho_id) {
            return response(['message' => "Chưa chọn kho"], 400);
        }
        if ($user->role_id != 1 && $user->role_id != 2) {
            return response(['message' => "Không có quyền nhập kho"], 402);
        }
        try {
            DB::beginTransaction();
            DonHangNhaCungCap::where('id', $id)->first()->update(['trang_thai' => 'nhap_kho']);
            PhieuNhapKho::create(['don_hang_id' => $id, 'ma' => 'PNK' . $id, 'user_id' => $user->id, 'kho_id' => $kho_id]);
            $hangHoa = SanPhamDonHangNhaCungCap::where('don_hang_id', $id)->get();
            foreach ($hangHoa as $item) {
                $checkKho = HangTonKho::where('san_pham_id', $item->san_pham_id)->where('kho_id', $kho_id)->first();
                if ($checkKho) {
                    $checkKho->update(['so_luong' => $checkKho->so_luong + $item->so_luong]);
                } else {
                    HangTonKho::create(['san_pham_id' => $item->san_pham_id, 'so_luong' => $item->so_luong, 'kho_id' => $kho_id]);
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể tạo phiếu nhập'], 500);
        }
    }
}
