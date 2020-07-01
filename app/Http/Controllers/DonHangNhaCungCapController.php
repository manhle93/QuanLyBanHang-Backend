<?php

namespace App\Http\Controllers;

use App\DonHangNhaCungCap;
use App\SanPhamDonHangNhaCungCap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use DB;

class DonHangNhaCungCapController extends Controller
{
    public function getDonHang(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = DonHangNhaCungCap::with('user', 'sanPhams');
        $donHang = [];
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
        if(!$user){
            return response(['message' => 'Chưa đăng nhập'], 500);
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
                'user_id' => $user->id,
                'don_hang' => 'moi_tao'
            ]);
            foreach($data['danhSachHang'] as $item){
                SanPhamDonHangNhaCungCap::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'don_gia' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_hang_id' => $donHang->id
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể thêm mới'], 500);
        }
    }
    public function getChiTietDonHang($id){
       $donHang = DonHangNhaCungCap::with('sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        return response(['data' => $donHang], 200);
    }
}
