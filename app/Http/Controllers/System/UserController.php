<?php

namespace App\Http\Controllers\System;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $user = auth()->user();
        $query = User::query()->with('tinhThanh', 'toaNha');
        $search = $request->get('search');
        $active = $request->get('active');
        $toa_nha_id = $request->get('toa_nha_id');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        if (!empty($active)) {
            $query->where('active', $active);
        }
        if (!empty($toa_nha_id)) {
            $query->whereIn('toa_nha_id', $toa_nha_id);
        }
        if (!empty($tinh_thanh_id)) {
            $query->whereIn('tinh_thanh_id', $tinh_thanh_id);
        }
        if($user->tinh_thanh_id && $user->role_id == 2){
            $query->where('tinh_thanh_id', $user->tinh_thanh_id);
        }
        if (isset($search)) {
            $search = trim($search);
            $query->where('name', 'ilike', "%{$search}%");
            $query->orWhere('phone', 'ilike', "%{$search}%");
            $query->orWhere('email', 'ilike', "%{$search}%");
            $query->orWhere('username', 'ilike', "%{$search}%");
            $query->orWhere('search', 'ilike', "%{$search}%");
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
            'password_confirmation'=>'required|same:password'
        ],[
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
            foreach ($validator->errors()->all() as $it){
                $loi = $loi.''.$it.", ";
            };
            return response()->json([
                'code' => 400,
                'message' => $loi,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $check_user = User::where('username','ilike', $data['username'])->get()->count();
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

    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $data['search'] = convert_vi_to_en($request->name);
        $validator = Validator::make($data, [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
        ],[
            'password_confirmation.same' => 'Mật khẩu 2 lần nhập không khớp',
            'name.required' => 'Tên không thể bỏ trống',
            'email.required' => 'Email không thể bỏ trống',
            'username.required' => 'Tên đăng nhập không thể bỏ trống',
            'role_id.required' => 'Quyền không thể bỏ trống',
        ]);
        if(isset($data['password'])){
            $validator = Validator::make($data, [
                'name' => 'required',
                'username' => 'required',
                'email' => 'required|email',
                'role_id' => 'required',
                'password' => 'min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'password_confirmation' => 'same:password',
            ],[
                'password.regex' => 'Mật khẩu không đủ mạnh',
                'password.min' => 'Mật khẩu tối thiểu 6 ký tự',
                'password_confirmation.same' => 'Mật khẩu 2 lần nhập không khớp',
                'name.required' => 'Tên không thể bỏ trống',
                'email.required' => 'Email không thể bỏ trống',
                'username.required' => 'Tên đăng nhập không thể bỏ trống',
                'role_id.required' => 'Quyền không thể bỏ trống',
            ]);
        }

        $check_email = User::where('email','ilike', $data['email'])->where('id', '<>', $id)->get()->count();
        if ($check_email != 0) {
            return response()->json([
                'code' => 400,
                'message' => __('Email đã tồn tại'),
                'data' => [],
            ], 400);
        }
        if ($validator->fails()) {
            $loi = "";
            foreach ($validator->errors()->all() as $it){
                $loi = $loi.''.$it.", ";
            };
            return response()->json([
                'code' => 400,
                'message' => $loi,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $check_user = User::where('username', 'ilike',$data['username'])->where('id', '<>', $id)->get()->count();
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
            } else {
                unset($data['password']);
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
            $name = time().'.'.$image->getClientOriginalExtension();
            $image->move('storage/images/avatar/', $name);
            $user = User::find($id);
            $user->update(['avatar_url' => 'storage/images/avatar/'.$name]);

            return 'storage/images/avatar/'.$name;
        }
    }
    public function getKhachHang($id){
       $khach_hang =  User::where('toa_nha_id', $id)->get();
        return \response(['data'=>$khach_hang],200);
    }
}
