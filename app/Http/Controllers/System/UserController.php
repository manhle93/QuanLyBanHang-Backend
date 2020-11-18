<?php

namespace App\Http\Controllers\System;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\KhachHang;
use App\NhaCungCap;
use App\Scopes\ActiveScope;
use App\ThongTinNhanVien;
use App\Token;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['userActivity'])->only('index');
    }
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $user = auth()->user();
        $query = User::query()->with('khachHang:id,user_id,dia_chi', 'role', 'nhanVien');
        $search = $request->get('search');
        $active = $request->get('active');
        $role = $request->get('role');
        if (!empty($active)) {
            $query->where('active', $active);
        }
        if (!empty($role)) {
            $query->whereIn('role_id', $role);
        }
        if (isset($search)) {
            $search = trim($search);
            $query->where('name', 'ilike', "%{$search}%");
            $query->orWhere('phone', 'ilike', "%{$search}%");
            $query->orWhere('email', 'ilike', "%{$search}%");
            $query->orWhere('username', 'ilike', "%{$search}%");
        }
        $query->orderBy('updated_at', 'desc');
        $users = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $users,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $data['search'] = convert_vi_to_en($request->name);

        $validator = Validator::make($data, [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'role_id' => 'required',
            'password_confirmation' => 'required|same:password'
        ], [
            'password.regex' => 'Mật khẩu không đủ mạnh',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự',
            'password_confirmation.same' => 'Mật khẩu 2 lần nhập không khớp',
            'name.required' => 'Tên không thể bỏ trống',
            'email.required' => 'Email không thể bỏ trống',
            'role_id.required' => 'Quyền không thể bỏ trống',
            'username.required' => 'Tên đăng nhập không thể bỏ trống',
        ]);
        $check_email = User::where('email', 'ilike', $data['email'])->get()->count();
        if ($check_email != 0) {
            return response()->json([
                'code' => 400,
                'message' => __('Email đã tồn tại'),
                'data' => [],
            ], 400);
        }
        if ($validator->fails()) {
            $loi = "";
            foreach ($validator->errors()->all() as $it) {
                $loi = $loi . '' . $it . ", ";
            };
            return response()->json([
                'code' => 400,
                'message' => $loi,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $check_user = User::where('username', 'ilike', $data['username'])->get()->count();
        if ($check_user != 0) {
            return response()->json([
                'code' => 400,
                'message' => __('Tên đăng nhập đã tồn tại'),
                'data' => [],
            ], 400);
        }
        try {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $user['active'] = true;
            $user = User::create($data);

            return response()->json([
                'message' => 'Thành công',
                'data' => $user,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo người dùng',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }
    public function logOutAllDecevice($id)
    {
        $tokens =  Token::where('user_id', $id)->first();
        if (!$tokens || !$tokens->tokens) {
            return response(['message' => 'Chưa có thiết bị nào đăng nhập'], 200);
        }
        $tokensArr = json_decode($tokens->tokens);
        try {
            foreach ($tokensArr as $item) {
                $tk =  JWTAuth::setToken($item)->getToken();
                JWTAuth::invalidate($tk);
            }
            $tokens->delete();
            return response(['message' => 'Đã đăng xuất trên tất cả các thiết bị'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể đăng xuất trên tất cả các thiết bị'], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $data['search'] = convert_vi_to_en($request->name);
        $validator = Validator::make($data, [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
        ], [
            'password_confirmation.same' => 'Mật khẩu 2 lần nhập không khớp',
            'name.required' => 'Tên không thể bỏ trống',
            'email.required' => 'Email không thể bỏ trống',
            'username.required' => 'Tên đăng nhập không thể bỏ trống',
            'role_id.required' => 'Quyền không thể bỏ trống',
        ]);
        if (isset($data['password'])) {
            $validator = Validator::make($data, [
                'name' => 'required',
                'username' => 'required',
                'email' => 'required|email',
                'role_id' => 'required',
                'password' => 'min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'password_confirmation' => 'same:password',
            ], [
                'password.regex' => 'Mật khẩu không đủ mạnh',
                'password.min' => 'Mật khẩu tối thiểu 6 ký tự',
                'password_confirmation.same' => 'Mật khẩu 2 lần nhập không khớp',
                'name.required' => 'Tên không thể bỏ trống',
                'email.required' => 'Email không thể bỏ trống',
                'username.required' => 'Tên đăng nhập không thể bỏ trống',
                'role_id.required' => 'Quyền không thể bỏ trống',
            ]);
        }

        $check_email = User::where('email', 'ilike', $data['email'])->where('id', '<>', $id)->get()->count();
        if ($check_email != 0) {
            return response()->json([
                'code' => 400,
                'message' => __('Email đã tồn tại'),
                'data' => [],
            ], 400);
        }
        if ($validator->fails()) {
            $loi = "";
            foreach ($validator->errors()->all() as $it) {
                $loi = $loi . '' . $it . ", ";
            };
            return response()->json([
                'code' => 400,
                'message' => $loi,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $check_user = User::where('username', 'ilike', $data['username'])->where('id', '<>', $id)->get()->count();
        if ($check_user != 0) {
            return response()->json([
                'code' => 400,
                'message' => __('Tên đăng nhập đã tồn tại'),
                'data' => [],
            ], 400);
        }
        try {
            $user = User::where('id', $id)->first();
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
                $user->password = $data['password'];
                $user->save();
                $this->logOutAllDecevice($id);
            } else {
                unset($data['password']);
            }
            $khachHang = KhachHang::withoutGlobalScope(ActiveScope::class)->where('user_id', $id)->first();
            if ($khachHang) {
                $khachHang->update(['active' => $data['active']]);
            }
            $user->update($data);

            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi cập nhật',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            User::find($id)->delete();
            return response()->json([
                'message' => 'Xóa người dùng thành công',
                'code' => 200,
                'data' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi, không thể xóa người dùng',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function uploadAvatar(Request $request, $id)
    {
        if ($request->file) {
            $image = $request->file;
            $name = time() . '.' . $image->getClientOriginalExtension();
            $image->move('storage/images/avatar/', $name);
            if ($id != 'false') {
                $user = User::find($id);
                $user->update(['avatar_url' => 'storage/images/avatar/' . $name]);
            }
            return 'storage/images/avatar/' . $name;
        }
    }
    public function getKhachHang($id)
    {
        $khach_hang =  User::where('toa_nha_id', $id)->get();
        return \response(['data' => $khach_hang], 200);
    }

    public function getShipper()
    {
        $data = User::where('role_id', 5)->get();
        return $data;
    }

    public function dangKyNhaCungCap(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ], [
            'password.regex' => 'Mật khẩu không đủ mạnh',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự',
            'password_confirmation.same' => 'Mật khẩu 2 lần nhập không khớp',
            'name.required' => 'Tên không thể bỏ trống',
            'email.required' => 'Email không thể bỏ trống',
            'username.required' => 'Tên đăng nhập không thể bỏ trống',
        ]);
        $check_email = User::where('email', 'ilike', $data['email'])->first();
        if ($check_email) {
            return response()->json([
                'code' => 400,
                'message' => __('Email đã tồn tại'),
                'data' => [],
            ], 400);
        }
        if ($validator->fails()) {
            $loi = "";
            foreach ($validator->errors()->all() as $it) {
                $loi = $loi . '' . $it . ", ";
            };
            return response()->json([
                'code' => 400,
                'message' => $loi,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $check_user = User::where('username', 'ilike', $data['username'])->first();
        if ($check_user) {
            return response()->json([
                'code' => 400,
                'message' => __('Tên đăng nhập đã tồn tại'),
                'data' => [],
            ], 400);
        }
        try {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $data['active'] = true;
            $data['role_id'] = 3;
            $user = User::create($data);
            NhaCungCap::create([
                'ten' => $data['name'],
                'ma' => 'NCC' . time(),
                'trang_thai' => 'moi_tao',
                'user_id' => $user->id,
                'email' => $data['email']
            ]);
            return response()->json([
                'message' => 'Thành công',
                'data' => $user,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo người dùng',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function updateNhanVien(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'user_id' => 'required',
            'email' => 'required',
            'name' => 'required',
            'phone' => 'required',
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
        try {
            User::find($data['user_id'])->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);
            $nhanVien =  ThongTinNhanVien::where('user_id', $data['user_id'])->first();
            if ($nhanVien) {
                $nhanVien->update([
                    'dia_chi' => $data['dia_chi'],
                    'so_cmt' => $data['so_cmt'],
                    'ngay_bat_dau_lam_viec' => $data['ngay_bat_dau_lam_viec'],
                    'so_dien_thoai_nguoi_than' => $data['so_dien_thoai_nguoi_than'],
                ]);
            } else {
                ThongTinNhanVien::create([
                    'user_id' => $data['user_id'],
                    'dia_chi' => $data['dia_chi'],
                    'so_cmt' => $data['so_cmt'],
                    'ngay_bat_dau_lam_viec' => $data['ngay_bat_dau_lam_viec'],
                    'so_dien_thoai_nguoi_than' => $data['so_dien_thoai_nguoi_than'],
                ]);
            }
            DB::commit();
            return response(['message' => 'Thanh cong'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể cập nhật thông tin'], 500);
        }
    }
}
