<?php

namespace App\Http\Controllers;

use App\DonDatHang;
use App\LichSuDangNhap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Token;

class ShiperController extends Controller
{
    public function loginShiper(Request $request)
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
        if (auth()->user()->role_id != 5) {
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
        try {
            $user = auth()->user();
            $userToken = Token::where('user_id', $user->id)->first();
            DB::beginTransaction();
            if ($userToken && $userToken->tokens) {
                $data = json_decode($userToken->tokens);
                $data[] = $token;
                $userToken->update(['tokens' => json_encode($data)]);
            } else {
                if ($userToken && !$userToken->tokens) {
                    $userToken->delete();
                }
                $tokenArr[] = $token;
                Token::create([
                    'user_id' => $user->id,
                    'tokens' => json_encode($tokenArr)
                ]);
            }
            DB::commit();
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ]);
        }
    }

    public function getDonHang()
    {
        $user = auth()->user();
        return DonDatHang::with('khachHang')->where('nhan_vien_giao_hang', $user->id)->get();
    }

    public function xuLyDon(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'don_hang_id' => 'required',
            'trang_thai' => 'required',
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
        $donHang = DonDatHang::where('id', $data['don_hang_id'])->first();
        if (!$donHang) {
            return response(['message' => 'Đơn hàng không tồn tại'], 404);
        }
        $user = auth()->user();
        if (!$user || $user->id != $donHang->nhan_vien_giao_hang) {
            return response(['message' => 'Không có quyền thực hiện'], 403);
        }
        $trangThais = ['nhan_don', 'tu_choi', 'hoan_thanh', 'huy_don'];
        if (!in_array($data['trang_thai'], $trangThais)) {
            return response(['message' => 'Trạng thái không hợp lệ'], 422);
        }
        try {
            $donHang->update(['trang_thai_giao_hang' => $data['trang_thai']]);
            return response(['message' => 'Success'], 200);
        } catch (\Exception $e) {
            dd($e);
            return response(['message' => 'Không thể thực hiện'], 500);
        }
    }
}
