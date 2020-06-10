<?php

namespace App\Http\Controllers;

use App\DanhMucSanPham;
use Illuminate\Http\Request;
use Validator;


class DanhMucSanPhamController extends Controller
{
    public function getDanhMucSanPham(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
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
        $data = $query->paginate($perPage, ['*'], 'page', $page);

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
    public function editDanhMucSanPham($id, Request $request)
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
            $user = DanhMucSanPham::where('id', $id)->first->update([
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
                'message' => 'Lỗi ! Không thể cập nhật danh mục sản phẩm',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }
}
