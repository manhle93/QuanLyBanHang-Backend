<?php

namespace App\Http\Controllers;

use App\Kho;
use Illuminate\Http\Request;
use Validator;

class KhoController extends Controller
{
    public function getKho(Request $request){
        $search = $request->get('search');
       $kho = Kho::get();
       if(isset($search)){
           $search = trim($search);
           $query = Kho::query();
           $query = Kho::where(function ($query) use ($search) {
            $query->where('ten', 'ilike', "%{$search}%")
            ->orWhere('ma', 'ilike', "%{$search}%")
            ->orWhere('dia_chi', 'ilike', "%{$search}%")
            ->orWhere('so_dien_thoai', 'ilike', "%{$search}%")
            ->orWhere('nguoi_quan_ly', 'ilike', "%{$search}%")
            ->orWhere('mo_ta', 'ilike', "%{$search}%");
           });
           $kho = $query->get();
       }
       return $kho;
    }
    public function addKho(Request $request){
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
          Kho::create([
                'ten' => $data['ten'],
                'ma' => $data['ma'],
                'dia_chi' => $data['dia_chi'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'nguoi_quan_ly' => $data['nguoi_quan_ly'],
                'mo_ta' => $data['mo_ta'],
                'trang_thai' => $data['trang_thai'],
            ]);
            return response(['message' => 'Thành công'], 200);   
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm kho hàng'], 500);
        }
    }
    public function editKho($id, Request $request){
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
        try {
          Kho::where('id', $id)->first()->update([
                'ten' => $data['ten'],
                'ma' => $data['ma'],
                'dia_chi' => $data['dia_chi'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'nguoi_quan_ly' => $data['nguoi_quan_ly'],
                'mo_ta' => $data['mo_ta'],
                'trang_thai' => $data['trang_thai'],
            ]);
            return response(['message' => 'Thành công'], 200);   
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật kho hàng'], 500);
        }
    }
    public function xoaKho($id){
        try{
            Kho::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        }catch (\Exception $e){
            return response(['message' => 'Không thể xóa kho hàng này'], 500);
        }
    }
}
