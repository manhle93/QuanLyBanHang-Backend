<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ThietBiQuay;
use Validator;

class ThietBiQuayController extends Controller
{
    public function getThietBiQuay(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $user = auth()->user();
        $query = ThietBiQuay::query()->with('loaiThietBi','loaiMayQuay', 'tinhThanh');

        // $search = $request->get('search');
        // $toa_nha_id = $request->get('toa_nha_id');
        // if (!empty($user->toa_nha_id)) {
        //     $query->where('toa_nha_id', $user->toa_nha_id);
        // } else {
        //     if (!empty($toa_nha_id)) {
        //         $query->where('toa_nha_id', $toa_nha_id);
        //     }
        // }

        // if (isset($search)) {
        //     $search = trim($search);
        //     $query->where('name', 'ilike', "%{$search}%");
        //     $query->orWhere('phone', 'ilike', "%{$search}%");
        // }

        // $query->with('ThietBiQuay');
        $query->orderBy('updated_at', 'desc');
        $thietbiquay = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $thietbiquay,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function deleteThietBiQuay(Request $request, $id)
    {
        try {
            ThietBiQuay::find($id)->delete();

            return response()->json([
                'message' => 'Xóa thành công',
                'code' => 200,
                'data' => [],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi xóa dữ liệu',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function show($id)
    {
        $donvi = ThietBiQuay::find($id);
        return response()->json([
            'data' => $donvi,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }


    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm thiết bị quay'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $data['ma'] = trim($data['ma']);
            if(ThietBiQuay::where('ma','ilike', $data['ma'])->first()){
                return response()->json([
                    'message' => "Mã thiết bị quay đã tồn tại",
                    'code' => 400,
                    'data' => ''
                ],400);
            }
            if(!empty($user)){
                $data['tinh_thanh_id']=$user->tinh_thanh_id;
            }
            $ThietBiQuay = ThietBiQuay::create($data);

            return response()->json([
                'message' => 'Thành công',
                'data' => $ThietBiQuay,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo thiết bị',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'lat' => 'required',
            'long' => 'required',
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
            $data['ma'] = trim($data['ma']);
            if (ThietBiQuay::where('ma','ilike', $data['ma'])->where('id', '<>',$id)->first()){
                return response(['message' => 'Mã thiết bị quay đã tồn tại'], 400);
            }
            ThietBiQuay::where('id', $id)->first()->update($data);
            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi cập nhật',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

}
