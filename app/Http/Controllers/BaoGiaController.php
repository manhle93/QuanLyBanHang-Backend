<?php

namespace App\Http\Controllers;

use App\BaoGia;
use App\SanPham;
use App\SanPhamBaoGia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use DB;
use Exception;

class BaoGiaController extends Controller
{
    public function addBaoGia(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
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
        if ($user->role_id == 3) {
            $data['nha_cung_cap_id'] = null;
        }
        if ($user->role_id != 3 && $user->role_id != 2 && $user->role_id != 1) {
            return response(['message' => 'Không có quyền'], 4001);
        }
        try {
            DB::beginTransaction();
            $donHang = BaoGia::create([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'ghi_chu' => $data['ghi_chu'],
                'user_id' => $data['nha_cung_cap_id'] ? $data['nha_cung_cap_id'] : $user->id,
            ]);
            foreach ($data['danhSachHang'] as $item) {
                SanPhamBaoGia::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'don_gia' => $item['don_gia'],
                    'bao_gia_id' => $donHang->id,
                    'gia_khuyen_cao' => $item['gia_khuyen_cao']
                ]);
            }
            DB::commit();
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể thêm mới'], 500);
        }
    }

    public function getBaoGia(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = BaoGia::with('user');
        $search = $request->get('search');
        $data = [];
        if (isset($search)) {
            $search = trim($search);
            $query->where('ten', 'ilike', "%{$search}%");
        }
        if ($user->role_id == 1 || $user->role_id == 2) {
            $data = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function getChiTietBaoGia($id)
    {
        $donHang = BaoGia::with('user', 'sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        return response(['data' => $donHang], 200);
    }

    public function xoaBaoGia($id)
    {
        try {
            BaoGia::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa'], 500);
        }
    }

    public function duyetBaoGia($id, Request $request)
    {
        $sanPham = $request->get('san_phams');
        if (isset($sanPham) && count($sanPham) > 0) {
            try {
                SanPhamBaoGia::whereIn('id', $sanPham)->update(['lua_chon' => true]);
                return response(['message' => 'Thành công'], 200);
            } catch (\Exception $e) {
                return response(['message' => 'Không thể duyệt báo giá'], 500);
            }
        }
        return;
    }
    public function capNhatGiaBan(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'id' => 'required',
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
        try {
            SanPham::where('id', $data['id'])->update(['gia_ban' => $data['gia_ban']]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật giá bán'], 500);
        }
    }

    public function getSanPhamBaoGiaNhaCungCap(Request $request){
        $nhaCungCapID = $request->get('nha_cung_cap_id');
        $data = [];
        if(!$nhaCungCapID){
            return $data;
        }
        $query = SanPhamBaoGia::with('baoGia', 'sanPham:id,ten_san_pham,don_vi_tinh')->where('lua_chon', true);
        $query = $query->whereHas('baoGia', function ($query) use ($nhaCungCapID){
            $query->where('user_id', $nhaCungCapID);
        });
        $data = $query->get();
        return $data;
    }
}
