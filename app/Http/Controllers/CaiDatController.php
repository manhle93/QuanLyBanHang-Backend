<?php

namespace App\Http\Controllers;

use App\MonNgonMoiNgay;
use App\SanPham;
use App\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class CaiDatController extends Controller
{
    public function uploadAnh(Request $request)
    {
        if ($request->file) {
            $image = $request->file;
            $name = time() . '.' . $image->getClientOriginalExtension();
            $image->move('storage/images/avatar/', $name);
            return 'storage/images/avatar/' . $name;
        }
    }
    public function addSilder(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'url_slider' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Hình ảnh không tồn tại'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 401);
        }
        try {
            Slider::create(
                [
                    'hinh_anh' => $data['url_slider'],
                    'dong_chu' => $data['dong_chu'],
                    'link' => $data['link']
                ]
            );
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm mới'], 500);
        }
    }

    public function getSilder()
    {
        return Slider::get();
    }
    public function updateSlider(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'url_slider' => 'required',
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Hình ảnh không tồn tại'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 401);
        }
        try {
            Slider::find($id)->update(
                [
                    'hinh_anh' => $data['url_slider'],
                    'dong_chu' => $data['dong_chu'],
                    'link' => $data['link']
                ]
            );
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cap nhat'], 500);
        }
    }
    public function xoaSilder($id)
    {
        try {
            Slider::find($id)->delete();
            return response(['message' => 'Thanh cong'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'khong the xoa'], 500);
        }
    }

    public function addMonNgonMoiNgay(Request $request)
    {
        $data = $request->get('data');
        try {
            MonNgonMoiNgay::truncate();
            if (isset($data) && count($data) > 0) {
                foreach ($data as $item) {
                    MonNgonMoiNgay::create([
                        'san_pham_id' => $item
                    ]);
                }
            }
            return response(['message' => 'Thanh cong'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'khong the cap nhat'], 500);
        }
    }
    public function getMonNgonMoiNgay(){
      $sanPhamID = MonNgonMoiNgay::select('san_pham_id')->pluck('san_pham_id')->toArray();
      return SanPham::with('danhMuc')->whereIn('id', $sanPhamID)->get();
    }
}
