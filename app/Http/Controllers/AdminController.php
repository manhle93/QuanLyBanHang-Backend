<?php

namespace App\Http\Controllers;

use App\TinhThanh;
use Illuminate\Http\Request;
use App\Config;
use App\QuanHuyen;
use App\Role;
use App\User;
use Illuminate\Support\Facades\DB;
use Validator;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function addConfig(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();
        $data['company_id'] = $user->company_id;
        $validator = Validator::make($data, [
            'name' => 'required',
            'value' => 'required',
            'company_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể khởi tạo'),
                'data' => [
                    $validator->errors()->all()
                ]
            ], 400);
        }
        try {
            $config = Config::create($data);
            return response()->json([
                'message' => 'Thành công',
                'code' => 200,
                'data' => $config
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể khởi tạo',
                'code' => 500,
                'data' => $e
            ], 500);
        }
    }
    public function index()
    {
        $user = auth()->user();
        $info = Config::where('company_id', $user->company_id)->get();
        return response()->json([
            'data' => $info,
            'code' => 200,
            'message' => 'Thành công'
        ], 200);
    }

    public function getPolygon()
    {
        $user = auth()->user();
        $tinh_thanh = null;
        if(isset($user->tinh_thanh_id)){
            $tinh_thanh = TinhThanh::where('id',$user->tinh_thanh_id)->select(DB::raw('st_asgeojson(geom)'))->first();
        }
        return response()->json([
            'data' => $tinh_thanh,
            'code' => 200,
            'message' => 'Thành công'
        ], 200);
    }
    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $user = auth()->user();
        $data['company_id'] = $user->company_id;
        $validator = Validator::make($data, [
            'name' => 'required',
            'value' => 'required',
            'company_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể cập nhật'),
                'data' => [
                    $validator->errors()->all()
                ]
            ], 400);
        }
        try {
            Config::where('id', $id)->update($data);
            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi cập nhật',
                'code' => 500,
                'data' => $e
            ], 500);
        }
    }
    public function delete($id)
    {
        try {
            Config::find($id)->delete();
            return response()->json([
                'message' => 'Xóa thành công',
                'code' => 200,
                'data' => ''
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi, không thể xóa',
                'code' => 500,
                'data' => $e
            ], 500);
        }
    }
    public function getInfor()
    {
        $user = auth()->user();
        if (!empty($user->quan_huyen_id)) {
            $user['quanhuyen'] = QuanHuyen::where('id', $user->quan_huyen_id)->select('id', 'name', 'code')->first();
        } else {
            $user['quanhuyen'] = null;
        }
        if (!empty($user->tinh_thanh_id)) {
            $user['tinhthanh'] = TinhThanh::where('id', $user->tinh_thanh_id)->select('id', 'name', 'code')->first();
        } else {
            $user['tinhthanh'] = null;
        }

        $user['role'] = Role::where('id', $user->role_id)->first();
        return response()->json([
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
            'data' => $user
        ], 200);
    }

    public function editInfor(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();
        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required',
            'username' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể cập nhật'),
                'data' => [
                    $validator->errors()->all()
                ]
            ], 400);
        }
        try {
            User::where('id', $user->id)->first()->update(
                [
                    'username' => $data['username'],
                    "name" => $data['name'],
                    "email" => $data['email'],
                    "phone" => $data['phone'],
                ]
            );
            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi cập nhật',
                'code' => 500,
                'data' => $e
            ], 500);
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
            'newPassword' => 'required|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'reNewPassword' => 'required|same:newPassword',
        ],[
            'newPassword.regex' => 'Mật khẩu không đủ mạnh',
            'newPassword.min' => 'Mật khẩu tối thiểu 6 ký tự',
            'newPassword.required' => 'Chưa nhập mật khẩu mới',
            'oldPassword.required' => 'Chưa nhập mật khẩu cũ',
            'reNewPassword.required' => 'Hãy nhập lại mật khẩu',
            'reNewPassword.same' => 'Mật khẩu 2 lần nhập không khớp',
        ]);
        if ($validator->fails()) {
            $loi = "";
            foreach ($validator->errors()->all() as $it){
                $loi = $loi.''.$it.", ";
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

    public function uploadAvatar(Request $request)
    {
        if ($request->file) {
            $image = $request->file;
            $name = time() . '.' . $image->getClientOriginalExtension();
            $image->move('storage/images/avatar/', $name);
            $user = User::find(auth()->user()->id);
            $user->update(['avatar_url' => 'storage/images/avatar/' . $name]);
            return 'storage/images/avatar/' . $name;
        }
    }
}
