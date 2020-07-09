<?php

namespace App\Http\Controllers;

use App\BangGia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use DB;

class BangGiaController extends Controller
{
    public function addBangGia(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
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
        $ngayBatDau = null;
        $ngayKetThuc = null;
        try {
            if ($data['thoi_gian'] && count($data['thoi_gian'])) {
                $ngayBatDau = Carbon::parse($data['thoi_gian'][0])->timezone('Asia/Ho_Chi_Minh');
                $ngayKetThuc = Carbon::parse($data['thoi_gian'][1])->timezone('Asia/Ho_Chi_Minh');
            }
            BangGia::create([
                'ten' => $data['ten'],
                'ngay_bat_dau' => $ngayBatDau,
                'ngay_ket_thuc' =>  $ngayKetThuc,
                'ap_dung' => $data['ap_dung']
            ]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể tạo bảng giá'], 500);
        }
    }
    public function getBangGia(Request $request){
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = BangGia::query();
        $search = $request->get('search');
        $data = [];
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('ten', 'ilike', "%{$search}%");
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
    public function editBangGia($id, Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
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
        $ngayBatDau = null;
        $ngayKetThuc = null;
        try {
            if ($data['thoi_gian'] && count($data['thoi_gian'])) {
                $ngayBatDau = Carbon::parse($data['thoi_gian'][0])->timezone('Asia/Ho_Chi_Minh');
                $ngayKetThuc = Carbon::parse($data['thoi_gian'][1])->timezone('Asia/Ho_Chi_Minh');
            }
            BangGia::find($id)->update([
                'ten' => $data['ten'],
                'ngay_bat_dau' => $ngayBatDau,
                'ngay_ket_thuc' =>  $ngayKetThuc,
                'ap_dung' => $data['ap_dung']
            ]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật bảng giá'], 500);
        }
    }
    public function xoaBangGia($id){
        try{
            BangGia::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Không thể xóa bảng giá'], 500);
        }
    }
}
