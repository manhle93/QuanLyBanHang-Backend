<?php

namespace App\Http\Controllers;

use App\BangGia;
use App\BangGiaSanPham;
use App\SanPham;
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
    public function getBangGia(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = BangGia::with('sanPham');
        $search = $request->get('search');
        $data = [];
        if (isset($search)) {
            $search = trim($search);
            $query->where('ten', 'ilike', "%{$search}%");
        }
        if ($user->role_id == 1 || $user->role_id == 2) {
            $data = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        foreach ($data as $item) {
            $soSanPham = BangGiaSanPham::where('bang_gia_id', $item->id)->count();
            $item['so_san_pham'] = $soSanPham;
        }
        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
    public function editBangGia($id, Request $request)
    {
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
    public function xoaBangGia($id)
    {
        try {
            BangGia::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa bảng giá'], 500);
        }
    }

    public function addSanPhamBangGia($id, Request $request)
    {
        try {
            $data = $request->all();
            BangGiaSanPham::where('bang_gia_id', $id)->delete();
            if (count($data) > 0) {
                foreach ($data as $item) {
                    BangGiaSanPham::create(['san_pham_id' => $item['san_pham']['id'], 'bang_gia_id' => $id, 'gia_ban' => $item['gia_ban']]);
                }
            }
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật bảng giá'], 500);
        }
    }
    public function getSanPhamBangGia($id)
    {
        $data = BangGiaSanPham::with('sanPham')->where('bang_gia_id', $id)->get();
        return  response(['message' => 'Thành công', 'data' => $data], 200);
    }

    public function getSanPham(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = SanPham::with('bangGias', 'danhMuc');
        $search = $request->get('search');
        $data = [];
        if (isset($search)) {
            $search = trim($search);
            $query->where('ten_san_pham', 'ilike', "%{$search}%");
            $query->orWhere('mo_ta_san_pham', 'ilike', "%{$search}%");
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

    public function getBangGiaSanPham($id){
       $bangGia =  BangGiaSanPham::where('san_pham_id', $id)->pluck('bang_gia_id')->toArray();
       $data = BangGia::with('sanPham')->whereIn('id', $bangGia)->get();
       return $data;
    }
}
