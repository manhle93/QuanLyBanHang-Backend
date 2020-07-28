<?php

namespace App\Http\Controllers;

use App\BangGiaSanPham;
use App\KhachHang;
use App\NhaCungCap;
use App\NopTien;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Facades\DB;

class KhachHangNhaCungCapController extends Controller
{
    public function addKhachHang(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'email' => 'required',
            'so_dien_thoai' => 'required',
            'username' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
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
        if ($data['password'] != $data['password_confirmation']) {
            return response()->json([
                'message' => 'Mật khẩu 2 lần nhập khôn trùng khớp',
                'code' => 400,
                'data' => ''
            ], 400);
        };
        if (User::where('username', $data['username'])->first()) {
            return response()->json([
                'code' => 400,
                'message' => __('Tên đăng nhập đã tồn tại'),
                'data' => [],
            ], 400);
        }
        if (KhachHang::where('ma', $data['ma'])->first()) {
            return response()->json([
                'code' => 400,
                'message' => __('Mã khách hàng đã tồn tại'),
                'data' => [],
            ], 400);
        }
        DB::beginTransaction();
        try {
          $khachHang = KhachHang::create([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'dia_chi' => $data['dia_chi'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'ma_so_thue' => $data['ma_so_thue'],
                'email' => $data['email'],
                'facebook' => $data['facebook'],
                'nhom_id' => $data['nhom_id'],
                'gioi_tinh' => $data['gioi_tinh'],
                'ca_nhan' => $data['ca_nhan'],
                'ghi_chu' => $data['ghi_chu'],
                'ngay_sinh' => Carbon::parse($data['ngay_sinh'])->timezone('Asia/Ho_Chi_Minh'),
                'giao_dich_cuoi' => Carbon::parse($data['giao_dich_cuoi'])->timezone('Asia/Ho_Chi_Minh'),
                'so_tai_khoan' => $data['so_tai_khoan'], 
                'so_du' => $data['so_du'],
                'chuyen_khoan_cuoi' => Carbon::parse($data['chuyen_khoan_cuoi'])->timezone('Asia/Ho_Chi_Minh'),
                'loai_thanh_vien_id' => $data['loai_thanh_vien_id'],
                'tin_nhiem' => $data['tin_nhiem'],
                'diem_quy_doi' => $data['diem_quy_doi'],
                'tien_vay' => $data['tien_vay'],
                'trang_thai' => 'moi_tao',
                'nguoi_tao_id' => auth()->user()->id
            ]);
            $user = User::create([
                'username' => $data['username'],
                'name' => $data['ten'],
                'email' => $data['email'],
                'phone' => $data['so_dien_thoai'],
                'role_id' => 4,
                'dia_chi' => $data['dia_chi'],
                'password' => Hash::make($data['password']),
                'active' => false
            ]);
            $khachHang->update(['user_id' => $user->id]);
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể tạo khách hàng'], 500);
        }
    }

    public function editKhachHang($id, Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'so_dien_thoai' => 'required',
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
        if (KhachHang::where('ma', $data['ma'])->where('id', '<>', $id)->first()) {
            return response()->json([
                'code' => 400,
                'message' => __('Mã khách hàng đã tồn tại'),
                'data' => [],
            ], 400);
        }
        try {
            KhachHang::find($id)->update([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'dia_chi' => $data['dia_chi'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'ma_so_thue' => $data['ma_so_thue'],
                'email' => $data['email'],
                'facebook' => $data['facebook'],
                'nhom_id' => $data['nhom_id'],
                'gioi_tinh' => $data['gioi_tinh'],
                'ca_nhan' => $data['ca_nhan'],
                'ghi_chu' => $data['ghi_chu'],
                'ngay_sinh' => Carbon::parse($data['ngay_sinh'])->timezone('Asia/Ho_Chi_Minh'),
                'giao_dich_cuoi' => Carbon::parse($data['giao_dich_cuoi'])->timezone('Asia/Ho_Chi_Minh'),
                'so_tai_khoan' => $data['so_tai_khoan'],
                'so_du' => $data['so_du'],
                'chuyen_khoan_cuoi' => Carbon::parse($data['chuyen_khoan_cuoi'])->timezone('Asia/Ho_Chi_Minh'),
                'loai_thanh_vien_id' => $data['loai_thanh_vien_id'],
                'tin_nhiem' => $data['tin_nhiem'],
                'diem_quy_doi' => $data['diem_quy_doi'],
                'tien_vay' => $data['tien_vay'],
                'trang_thai' => $data['trang_thai'],
            ]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật khách hàng'], 500);
        }
    }

    public function xoaKhachHang($id){
        try{
            $khachHang = KhachHang::where('id', $id)->first();
            $khachHang->update(['active' => false]);
            User::find($khachHang->user_id)->update(['active' => false]);
            return response(['message' => 'Thành công'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Không thể xóa khách hàng'], 500);
        }
    }

    public function getKhachHang(Request $request){
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = KhachHang::query();
        $search = $request->get('search');
        $data = [];
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('ten', 'ilike', "%{$search}%");
                $query->orWhere('ma', 'ilike', "%{$search}%");
                $query->orWhere('dia_chi', 'ilike', "%{$search}%");
                $query->orWhere('so_dien_thoai', 'ilike', "%{$search}%");
                $query->orWhere('ma_so_thue', 'ilike', "%{$search}%");
                $query->orWhere('email', 'ilike', "%{$search}%");
            });
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

    public function getNhaCungCap(Request $request){
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = NhaCungCap::query();
        $search = $request->get('search');
        $data = [];
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('ten', 'ilike', "%{$search}%");
                $query->orWhere('ma', 'ilike', "%{$search}%");
                $query->orWhere('dia_chi', 'ilike', "%{$search}%");
                $query->orWhere('so_dien_thoai', 'ilike', "%{$search}%");
                $query->orWhere('ma_so_thue', 'ilike', "%{$search}%");
                $query->orWhere('email', 'ilike', "%{$search}%");
            });
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

    public function addNhaCungCap(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'email' => 'required',
            'so_dien_thoai' => 'required',
            'username' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
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
        if ($data['password'] != $data['password_confirmation']) {
            return response()->json([
                'message' => 'Mật khẩu 2 lần nhập khôn trùng khớp',
                'code' => 400,
                'data' => ''
            ], 400);
        };
        if (User::where('username', $data['username'])->first()) {
            return response()->json([
                'code' => 400,
                'message' => __('Tên đăng nhập đã tồn tại'),
                'data' => [],
            ], 400);
        }
        if (KhachHang::where('ma', $data['ma'])->first()) {
            return response()->json([
                'code' => 400,
                'message' => __('Mã nhà cung cấp đã tồn tại'),
                'data' => [],
            ], 400);
        }
        DB::beginTransaction();
        try {
          $khachHang = NhaCungCap::create([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'dia_chi' => $data['dia_chi'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'ma_so_thue' => $data['ma_so_thue'],
                'email' => $data['email'],
                'ghi_chu' => $data['ghi_chu'],
                'tin_nhiem' => $data['tin_nhiem'],
                'cong_ty' => $data['cong_ty'],
                'trang_thai' => $data['trang_thai'],
                'nguoi_tao_id' => auth()->user()->id
            ]);
            $user = User::create([
                'username' => $data['username'],
                'name' => $data['ten'],
                'email' => $data['email'],
                'phone' => $data['so_dien_thoai'],
                'role_id' => 3,
                'dia_chi' => $data['dia_chi'],
                'password' => Hash::make($data['password']),
                'active' => false
            ]);
            $khachHang->update(['user_id' => $user->id]);
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return response(['message' => 'Không thể tạo nhà cung cấp'], 500);
        }
    }

    public function editNhaCungCap($id, Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'so_dien_thoai' => 'required',
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
        if (NhaCungCap::where('ma', $data['ma'])->where('id', '<>', $id)->first()) {
            return response()->json([
                'code' => 400,
                'message' => __('Mã nhà cung cấp đã tồn tại'),
                'data' => [],
            ], 400);
        }
        try {
            NhaCungCap::find($id)->update([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'dia_chi' => $data['dia_chi'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'ma_so_thue' => $data['ma_so_thue'],
                'email' => $data['email'],
                'ghi_chu' => $data['ghi_chu'],
                'tin_nhiem' => $data['tin_nhiem'],
                'cong_ty' => $data['cong_ty'],
                'trang_thai' => $data['trang_thai'],
                'nguoi_tao_id' => auth()->user()->id
            ]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật nhà cung cấp'], 500);
        }
    }

    public function xoaNhaCungCap($id){
        try{
            $khachHang = NhaCungCap::where('id', $id)->first();
            $khachHang->update(['active' => false]);
            User::find($khachHang->user_id)->update(['active' => false]);
            return response(['message' => 'Thành công'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Không thể xóa nhà cung cấp'], 500);
        }
    }

    public function nopTien(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'id_user_khach_hang' => 'required',
            'so_tien' => 'required',
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
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            DB::beginTransaction();
            $khachHang = KhachHang::where('user_id', $data['id_user_khach_hang'])->first();
            $so_du = $khachHang->so_du;
            NopTien::create([
                'trang_thai' => 'nop_tien', 
                'id_user_khach_hang' => $data['id_user_khach_hang'],
                'user_id' => $user->id,
                'so_tien' => $data['so_tien'],
                'noi_dung' => $data['noi_dung'],
                'so_du' => $so_du + $data['so_tien'],
                'ma' => 'GD'.time()
            ]);
            $khachHang->update([
                'so_du' => $so_du + $data['so_tien'],
                'chuyen_khoan_cuoi' => Carbon::now()
            ]);
            DB::commit();
            return response(['message' => 'Thành công'],200);
        }catch(\Exception $e){
            DB::rollback();
            return response(['message' => 'Không thể nạp tiền'],500);
        }

    }

    public function hoanTac($id){
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            DB::beginTransaction();
            $nopTien = NopTien::where('id', $id)->first();
            $khachHang = KhachHang::where('user_id', $nopTien->id_user_khach_hang)->first();
            $so_du = $khachHang->so_du;
            NopTien::create([
                'trang_thai' => 'hoan_tac_nop_tien', 
                'noi_dung' => 'Hoàn tác cho giao dịch mã: '.$nopTien->ma,
                'id_user_khach_hang' => $nopTien->id_user_khach_hang,
                'user_id' => $user->id,
                'so_tien' => 0 - $nopTien->so_tien,
                'so_du' => $so_du - $nopTien->so_tien,
                'ma' => 'GD'.time()
                ]);

            $khachHang->update([
                'so_du' => $so_du - $nopTien->so_tien,
                'chuyen_khoan_cuoi' => Carbon::now()
            ]);
            $nopTien->update(['da_hoan_tien' => true]);
            DB::commit();
            return response(['message' => 'Thành công'],200);
        }catch(\Exception $e){
            DB::rollback();
            return response(['message' => 'Không thể hoàn tác'],500);
        }

    }

    public function lichSuNopTien(Request $request){
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = NopTien::with('nguoiTao:id,name', 'khachHang:user_id,ten');
        $search = $request->get('search');
        $data = [];
        $date = $request->get('date');
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('noi_dung', 'ilike', "%{$search}%");
                $query->orWhere('so_tien', 'ilike', "%{$search}%");
                $query->orWhere('ma', 'ilike', "%{$search}%");
            });
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if ($user->role_id == 1 || $user->role_id == 2) {
            $data = $query->orderBy('created_at', 'DESC')->paginate($perPage, ['*'], 'page', $page);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
}
