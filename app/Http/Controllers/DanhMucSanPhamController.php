<?php

namespace App\Http\Controllers;

use App\DanhMucSanPham;
use App\SanPham;
use Illuminate\Http\Request;
use Validator;


class DanhMucSanPhamController extends Controller
{
    public function getDanhMucSanPham(Request $request)
    {
        $query = DanhMucSanPham::query();
        $search = $request->get('search');
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('ten_danh_muc', 'ilike', "%{$search}%");
                $query->orWhere('mo_ta', 'ilike', "%{$search}%");
            });
        }
        $query->orderBy('updated_at', 'desc');
        $data = $query->get();
        foreach($data as $item){
            $sanPham = SanPham::where('danh_muc_id', $item->id)->count();
            $item['so_mat_hang'] = $sanPham;
        }
        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
        ], 200);
    }
    public function addDanhMucSanPham(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten_danh_muc' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm đơn vị'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $user = DanhMucSanPham::create([
                'ten_danh_muc' => $data['ten_danh_muc'],
                'mo_ta' => $data['mo_ta'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'user_tao' => $user->id
            ]);

            return response()->json([
                'message' => 'Thành công',
                'data' => $user,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo danh mục sản phẩm',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }
    public function editDanhMucSanPham(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten_danh_muc' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm đơn vị'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $user = DanhMucSanPham::where('id', $data['id'])->first()->update([
                'ten_danh_muc' => $data['ten_danh_muc'],
                'mo_ta' => $data['mo_ta'],
                'anh_dai_dien' => $data['anh_dai_dien'],
            ]);

            return response()->json([
                'message' => 'Thành công',
                'data' => $user,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể cập nhật danh mục sản phẩm',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function uploadAnhDanhMuc(Request $request)
    {
        if ($request->file) {
            $image = $request->file;
            $name = time() . '.' . $image->getClientOriginalExtension();
            $image->move('storage/images/avatar/', $name);
            // $danhMuc = DanhMucSanPham::find($id);
            // $danhMuc->update(['anh_dai_dien' => 'storage/images/avatar/' . $name]);
            return 'storage/images/avatar/' . $name;
        } else {
            return response(['message' => 'File không tồn tại'], 500);
        }
    }

    public function xoaDanhMuc($id)
    {
        try {
            DanhMucSanPham::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa danh mục này'], 500);
        }
    }
}
