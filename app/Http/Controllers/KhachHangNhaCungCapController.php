<?php

namespace App\Http\Controllers;

use App\BangGiaSanPham;
use App\DonDatHang;
use App\DonHangNhaCungCap;
use App\KhachHang;
use App\LichSuDangNhap;
use App\NhaCungCap;
use App\NopTien;
use App\SanPhamDonDatHang;
use App\ThanhToanNhaCungCap;
use App\TraHangNhaCungCap;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class KhachHangNhaCungCapController extends Controller
{
    public function addKhachHang(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
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
                'message' => 'Mật khẩu 2 lần nhập không trùng khớp',
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
        if (!isset($data['email']) || !$data['email']) {
            $data['email'] = $data['username'] . '@email.com';
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
                // 'so_du' => $data['so_du'],
                'chuyen_khoan_cuoi' => Carbon::parse($data['chuyen_khoan_cuoi'])->timezone('Asia/Ho_Chi_Minh'),
                'loai_thanh_vien_id' => $data['loai_thanh_vien_id'],
                'tin_nhiem' => $data['tin_nhiem'],
                'diem_quy_doi' => $data['diem_quy_doi'],
                'tien_vay' => $data['tien_vay'],
                'trang_thai' => 'moi_tao',
                'nguoi_tao_id' => auth()->user() ? auth()->user()->id : null,
            ]);
            $user = User::create([
                'username' => $data['username'],
                'name' => $data['ten'],
                'email' => $data['email'],
                'phone' => $data['so_dien_thoai'],
                'role_id' => 4,
                'dia_chi' => $data['dia_chi'],
                'password' => Hash::make($data['password']),
                'avatar_url' => $data['anh_dai_dien'],
                'active' => true
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

    public function xoaKhachHang($id)
    {
        try {
            $khachHang = KhachHang::where('id', $id)->first();
            $khachHang->update(['active' => false]);
            User::find($khachHang->user_id)->update(['active' => false]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa khách hàng'], 500);
        }
    }

    public function getKhachHang(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = KhachHang::with('user:id,name,avatar_url');
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
            foreach ($data as $item) {
                $hoaDonID = DonDatHang::where('trang_thai', 'hoa_don')->where('user_id', $item->user_id)->pluck('id');
                $tongTien = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDonID)->sum('doanh_thu');
                $item['tong_hoa_don'] = $tongTien;
            }
        }
        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function getNhaCungCap(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = NhaCungCap::with('user');
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
        foreach($data as $item){
            $item['cong_no'] = $this->tinhCongNoNCC($item['user_id'], null);
        }
        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
    function tinhCongNoNCC($user_id_nha_cung_cap, $date = null){
        $ncc = NhaCungCap::where('user_id', $user_id_nha_cung_cap)->first();
        if(!$ncc){
            return null;
        }
       $tongThanhToan = DonHangNhaCungCap::where('user_id', $user_id_nha_cung_cap)->sum('tong_tien');
       $thanhToanLan1 = DonHangNhaCungCap::where('user_id', $user_id_nha_cung_cap)->sum('da_thanh_toan');
       $thanhToanLan2 = ThanhToanNhaCungCap::where('nha_cung_cap_id', $ncc->id)->sum('thanh_toan');
       $traHang = TraHangNhaCungCap::where('nha_cung_cap_id', $ncc->id)->sum('tong_tien');

       if($date){
        $tongThanhToan = DonHangNhaCungCap::where('user_id', $user_id_nha_cung_cap)->where('created_at', '<=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())->sum('tong_tien');

        $thanhToanLan1 = DonHangNhaCungCap::where('user_id', $user_id_nha_cung_cap)->where('created_at', '<=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
        ->sum('da_thanh_toan');

        $thanhToanLan2 = ThanhToanNhaCungCap::where('nha_cung_cap_id', $ncc->id)->where('created_at', '<=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
        ->sum('thanh_toan');

        $traHang = TraHangNhaCungCap::where('nha_cung_cap_id', $ncc->id)->where('created_at', '<=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
        ->sum('tong_tien');
       }
       $congNo = $tongThanhToan- $thanhToanLan1 - $thanhToanLan2 - $traHang;
       return $congNo;
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
        if (NhaCungCap::where('ma', $data['ma'])->first()) {
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
                'anh_dai_dien' => $data['anh_dai_dien'],
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
                'avatar_url' => $data['anh_dai_dien'],
                'role_id' => 3,
                'dia_chi' => $data['dia_chi'],
                'password' => Hash::make($data['password']),
                'active' => $data['trang_thai'],
            ]);
            $khachHang->update(['user_id' => $user->id]);
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
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
            User::where('id', NhaCungCap::where('id', $id)->first()->user_id)->first()->update(['active' => $data['trang_thai']]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật nhà cung cấp'], 500);
        }
    }

    public function xoaNhaCungCap($id)
    {
        try {
            $khachHang = NhaCungCap::where('id', $id)->first();
            $khachHang->update(['active' => false]);
            User::find($khachHang->user_id)->update(['active' => false]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa nhà cung cấp'], 500);
        }
    }

    public function nopTien(Request $request)
    {
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
                'ma' => 'GD' . time()
            ]);
            $khachHang->update([
                'so_du' => $so_du + $data['so_tien'],
                'chuyen_khoan_cuoi' => Carbon::now()
            ]);
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể nạp tiền'], 500);
        }
    }

    public function hoanTac($id)
    {
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
                'noi_dung' => 'Hoàn tác cho giao dịch mã: ' . $nopTien->ma,
                'id_user_khach_hang' => $nopTien->id_user_khach_hang,
                'user_id' => $user->id,
                'so_tien' => 0 - $nopTien->so_tien,
                'so_du' => $so_du - $nopTien->so_tien,
                'ma' => 'GD' . time()
            ]);

            $khachHang->update([
                'so_du' => $so_du - $nopTien->so_tien,
                'chuyen_khoan_cuoi' => Carbon::now()
            ]);
            $nopTien->update(['da_hoan_tien' => true]);
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể hoàn tác'], 500);
        }
    }

    public function lichSuNopTien(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->get('per_page', 5);
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

    public function loginKhachHang(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password'  => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể đăng nhập'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $credentials = ['username' => $request->username, 'password' => $request->password];
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Sai tài khoản hoặc mật khẩu'], 401);
        }
        if (auth()->user()->role_id != 4) {
            return response(['message' => 'Chức năng đăng nhập chỉ dành cho khách hàng'], 500);
        }
        if (!auth()->user()->active) return response(['message' => 'Tài khoản chưa kích hoạt', 'user_id' => auth()->user()->id], Response::HTTP_NOT_ACCEPTABLE);

        LichSuDangNhap::create([
            'user_id' => auth()->user()->id,
            'type' => 'login',
            'thong_tin' => $_SERVER['HTTP_USER_AGENT']
        ]);
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    public function thongTinCaNhanKhachHang()
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập', 'data' => []], 400);
        }
        $khachHang = KhachHang::with('user')->where('user_id', $user->id)->first();
        // $donHang = DonDatHang::with('sanPhams', 'sanPhams.sanPham')->where('user_id', $user->id)->orderBy('updated_at', 'DESC')->get();
        $lichSuGD = NopTien::where('id_user_khach_hang', $user->id)->get();
        return response(['message' => 'Thành công', 'data' => $khachHang, 'don_hang' => [], 'giao_dich' => $lichSuGD], 200);
    }

    public function getThongTinDatHang()
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập', 'data' => []], 400);
        }
        $khachHang = KhachHang::with('user')->where('user_id', $user->id)->select('id', 'ten', 'so_dien_thoai', 'dia_chi')->first();
        return response(['message' => 'Thành công', 'data' => $khachHang], 200);
    }

    public function updateThongTinCaNhan(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'so_dien_thoai' => 'required',
            'email' => 'required',
            'dia_chi' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Thiếu dữ liệu'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $user = auth()->user();
        if (!$user || $user->role_id != 4) {
            return response(['message' => 'Tài khoản không tồn tại'], 400);
        }
        $khachHang = KhachHang::where('user_id', $user->id)->first();
        if (!$khachHang) {
            return response(['message' => 'Khách hàng không tồn tại'], 400);
        }
        try {
            $khachHang->update([
                'ten' => $data['ten'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'email' => $data['email'],
                'dia_chi' => $data['dia_chi'],
                'ngay_sinh' => $data['ngay_sinh'],
                'gioi_tinh' => $data['gioi_tinh'],
                'facebook' => $data['facebook'],

            ]);
            User::find($user->id)->update(['email' => $data['email']]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật'], 500);
        }
    }

    public function uploadAvatar(Request $request)
    {
        $user = auth()->user();
        try {
            if ($request->file) {
                $image = $request->file;
                $name = time() . '.' . $image->getClientOriginalExtension();
                $image->move('storage/images/avatar/', $name);
                User::find($user->id)->update(['avatar_url' => 'storage/images/avatar/' . $name]);
                KhachHang::where('user_id', $user->id)->first()->update(['anh_dai_dien' =>  'storage/images/avatar/' . $name]);
                return response(['message' => 'Thanh cong', 'data' => 'storage/images/avatar/' . $name], 200);
            }
        } catch (\Exception $e) {
            return response(['message' => 'Không thể uplaod ảnh'], 500);
        }
    }
    public function updatePassword(Request $request)
    {
        $data = $request->all();
        $oldPassword = $data['oldPassword'];
        $newPassword = $data['newPassword'];
        $reNewPasswork = $data['reNewPassword'];
        $validator = Validator::make($data, [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6',
            'reNewPassword' => 'required|same:newPassword',
        ], [
            'newPassword.min' => 'Mật khẩu tối thiểu 6 ký tự',
            'newPassword.required' => 'Chưa nhập mật khẩu mới',
            'oldPassword.required' => 'Chưa nhập mật khẩu cũ',
            'reNewPassword.required' => 'Hãy nhập lại mật khẩu',
            'reNewPassword.same' => 'Mật khẩu 2 lần nhập không khớp',
        ]);
        if ($validator->fails()) {
            $loi = "";
            foreach ($validator->errors()->all() as $it) {
                $loi = $loi . '' . $it . ", ";
            };
            return response()->json([
                'code' => 400,
                'message' =>  $loi,
                'data' => [
                    $validator->errors()->all()
                ]
            ], 400);
        }
        if (!Hash::check($oldPassword, Auth::user()->password)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không chính xác',
                'code' => 400,
                'data' => ''
            ], 400);
        };
        if ($newPassword == $oldPassword) {
            return response()->json([
                'message' => 'Mật khẩu mới trùng mật khẩu hiện tại',
                'code' => 400,
                'data' => ''
            ], 400);
        };
        if ($newPassword != $reNewPasswork) {
            return response()->json([
                'message' => 'Mật khẩu 2 lần nhập không khớp',
                'code' => 400,
                'data' => ''
            ], 400);
        };
        try {
            $request->user()->fill(['password' => Hash::make($newPassword)])->save();
            return response()->json([
                'message' => 'Cập nhật mật khẩu thành công',
                'code' => 200,
                'data' => ''
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi cập nhật',
                'code' => 500,
                'data' => $e
            ], 500);
        }
    }

    public function getDonHangMobile(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $month = $request->get('month');
        $year = $request->get('year');
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập', 'data' => []], 400);
        }
        $query =  DonDatHang::with('sanPhams', 'sanPhams.sanPham')->where('user_id', $user->id);
        if (isset($month) && isset($year)) {
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        }
        $donHang = $query->orderBy('updated_at', 'DESC')->paginate($perPage, ['*'], 'page', $page);
        return $donHang;
    }

    public function getGiaoDichMobile(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $month = $request->get('month');
        $year = $request->get('year');
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập', 'data' => []], 400);
        }
        $query = NopTien::where('id_user_khach_hang', $user->id);
        if (isset($month) && isset($year)) {
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        }
        $donHang = $query->orderBy('updated_at', 'DESC')->paginate($perPage, ['*'], 'page', $page);
        return $donHang;
    }
    public function getChiTietKhachHang(){
        $user = auth()->user();
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập', 'data' => []], 400);
        }
        $khachHang = KhachHang::with('user')->where('user_id', $user->id)->first();
        return $khachHang;
    }

    public function theoDoiCongNo(Request $request){
        $date = $request->get('date');
        $nha_cung_cap = $request->get('nha_cung_cap');
        if(!isset($nha_cung_cap)){
            return [];
        }
        $ncc= NhaCungCap::where('id', $nha_cung_cap)->first();
        if(!$ncc){
            return [];
        }
        $noDauKy = 0;
        $nhapHang = DonHangNhaCungCap::where('trang_thai', 'nhap_kho')->where('user_id', $ncc->user_id);
        $traHang = TraHangNhaCungCap::where('nha_cung_cap_id', $nha_cung_cap);
        $thanhToan = ThanhToanNhaCungCap::where('nha_cung_cap_id', $nha_cung_cap);
        if(isset($date)){
            $nhapHang->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
            ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());

            $traHang->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
            ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());

            $thanhToan->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
            ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());

            $noDauKy = $this->tinhCongNoNCC($ncc->user_id, $date);
        }
        return [
            'no_dau_ky' => $noDauKy,
            'nhap_hang' => $nhapHang->get(),
            'tra_hang' => $traHang->get(),
            'thanh_toan' => $thanhToan->get()
        ];
    }

    
}
