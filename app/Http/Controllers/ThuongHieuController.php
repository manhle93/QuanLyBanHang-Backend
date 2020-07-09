<?php

namespace App\Http\Controllers;

use App\ThuongHieu;
use Illuminate\Http\Request;
use Validator;

class ThuongHieuController extends Controller
{
    public function getThuongHieu()
    {
        $data = ThuongHieu::get();
        return response(['message' => 'Thanh cong', 'data' => $data], 200);
    }
    public function addThuongHieu(Request $request)
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
        try {
            ThuongHieu::create(['ten' => $data['ten'], 'mo_ta' => $data['mo_ta']]);
            return response(['message' => 'Thanh cong'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Khong the them moi'], 500);
        }
    }
    public function editThuongHieu($id, Request $request)
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
        try {
            ThuongHieu::where('id', $id)->first()->update(['ten' => $data['ten'], 'mo_ta' => $data['mo_ta']]);
            return response(['message' => 'Thanh cong'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Khong the cap nhat'], 500);
        }
    }
    public function xoaThuongHieu($id){
        try {
            ThuongHieu::find($id)->delete();
            return response(['message' => 'Thanh cong'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Khong the xoa'],500);
        }
    }
}
