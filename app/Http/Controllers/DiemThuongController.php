<?php

namespace App\Http\Controllers;

use App\DiemThuong;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiemThuongController extends Controller
{
    public function addDiemThuong(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'muc_hoa_don1' => 'required',
            'muc_hoa_don2' => 'required',
            'loai1' => 'required',
            'loai2' => 'required',
            'diem_thuong1' => 'required',
            'diem_thuong2' => 'required',
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
        if (count($data['thoi_gian']) != 2) {
            return response(['message' => 'Thời gian không hợp lệ'], 500);
        }
        $data['bat_dau'] = Carbon::parse($data['thoi_gian'][0]);
        $data['ket_thuc'] = Carbon::parse($data['thoi_gian'][1]);
        try {
            DiemThuong::create([
                'active' => $data['active'],
                'diem_thuong1' => $data['diem_thuong1'],
                'diem_thuong2' => $data['diem_thuong2'],
                'loai1' => $data['loai1'],
                'loai2' => $data['loai2'],
                'muc_hoa_don1' => $data['muc_hoa_don1'],
                'muc_hoa_don2' => $data['muc_hoa_don2'],
                'ten' => $data['ten'],
                'bat_dau' => $data['bat_dau'],
                'ket_thuc' => $data['ket_thuc'],
            ]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm mới'], 500);
        }
    }

    public function getCauHinhDiemthuong(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = DiemThuong::query();
        $date = $request->get('date');
        $search = $request->get('search');
        $donHang = [];
        if (isset($search)) {
            $query->where('ma', 'ilike', "%{$search}%")
                ->orWhere('mo_ta', 'ilike', "%{$search}%");
        }
        if (isset($date)) {
            $query->where('bat_dau', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('ket_thuc', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if ($user->role_id == 1 || $user->role_id == 2) {
            $donHang = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        return response()->json([
            'data' => $donHang,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function updateCauHinhDiemthuong($id, Request $request)
    {

        $data = $request->all();
        $user = auth()->user();
        if (!$user || $user->role_id != 1) {
            return response(['message' => 'Không có quyền'], 500);
        }
        $validator = Validator::make($data, [
            'ten' => 'required',
            'muc_hoa_don1' => 'required',
            'muc_hoa_don2' => 'required',
            'loai1' => 'required',
            'loai2' => 'required',
            'diem_thuong1' => 'required',
            'diem_thuong2' => 'required',
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
        try {
            if (count($data['thoi_gian']) != 2) {
                return response(['message' => 'Thời gian không hợp lệ'], 500);
            }
            $data['bat_dau'] = Carbon::parse($data['thoi_gian'][0]);
            $data['ket_thuc'] = Carbon::parse($data['thoi_gian'][1]);
            DiemThuong::find($id)->update([
                'active' => $data['active'],
                'diem_thuong1' => $data['diem_thuong1'],
                'diem_thuong2' => $data['diem_thuong2'],
                'loai1' => $data['loai1'],
                'loai2' => $data['loai2'],
                'muc_hoa_don1' => $data['muc_hoa_don1'],
                'muc_hoa_don2' => $data['muc_hoa_don2'],
                'ten' => $data['ten'],
                'bat_dau' => $data['bat_dau'],
                'ket_thuc' => $data['ket_thuc'],
            ]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật voucher'], 500);
        }
    }
    public function xoaCauHinh($id){
        try{
            DiemThuong::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Không thể xóa cấu hình'], 500);
        }
    }
}
