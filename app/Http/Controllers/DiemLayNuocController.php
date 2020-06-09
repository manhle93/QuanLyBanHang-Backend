<?php

namespace App\Http\Controllers;

use App\DiemLayNuoc;
use Illuminate\Http\Request;
use Validator;

class DiemLayNuocController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $data['search'] = convert_vi_to_en($request->ten.$request->dia_chi);
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'quan_huyen_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm điểm lấy nước'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            if (!empty($user->tinh_thanh_id)) {
                $data['tinh_thanh_id'] = $user->tinh_thanh_id;
            }
            $data['ma'] = trim($data['ma']);
            if(DiemLayNuoc::where('ma','ilike', $data['ma'])->first()){
                return response()->json([
                    'message' => "Mã điểm lấy nước đã tồn tại",
                    'code' => 400,
                    'data' => ''
                ],400);
            }
            if(!isset($data['don_vi_quan_ly'])){
                $data['don_vi_quan_ly'] = null;
            }
            $diem_lay_nuoc = DiemLayNuoc::create([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'dia_chi' => $data['dia_chi'],
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'quan_huyen_id' => $data['quan_huyen_id'],
                'don_vi_quan_ly' => $data['don_vi_quan_ly'],
                'loai' => $data['loai'],
                'description' => $data['description'],
                'status' => $data['status'],
                'important' => $data['important'],
                'lat' => $data['lat'],
                'long' => $data['long'],
                'search' => $data['search'],
                'don_vi_quan_ly_id' => $data['don_vi_quan_ly_id'],
                'kha_nang_cap_nuoc_cho_xe' => $data['kha_nang_cap_nuoc_cho_xe'],
                'hien_thi_tren_map' => $data['hien_thi_tren_map']
            ]);

            return response()->json([
                'message' => 'Thành công',
                'data' => $diem_lay_nuoc,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo điểm lấy nước',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function index(Request $request)
    {
        if (isset($request->per_page)) {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $quan_huyen_id = $request->get('quan_huyen_id');
            $tinh_thanh_id = $request->get('tinh_thanh_id');
            $don_vi_quan_ly_id = $request->get('don_vi_quan_ly_id');
            $query = DiemLayNuoc::query()->with('tinhThanh', 'quanHuyen');
            if (!empty($tinh_thanh_id)) {
                $query->where('tinh_thanh_id', $tinh_thanh_id);
            }
            if (!empty($don_vi_quan_ly_id)) {
                $query->where('don_vi_quan_ly_id', $don_vi_quan_ly_id);
            }
            if (!empty($quan_huyen_id)) {
                $query->where('quan_huyen_id', $quan_huyen_id);
            }
            if (isset($search)) {
                $search = trim($search);
                $query->where(function ($query) use ($search) {
                    $query->where('ma', 'ilike', "%{$search}%")
                    ->orWhere('ten', 'ilike', "%{$search}%")
                    ->orWhere('dia_chi', 'ilike', "%{$search}%")
                    ->orWhere('search', 'ilike', "%{$search}%");
                });
            }
            $query->orderBy('updated_at', 'desc');
            $tinhs = $query->paginate($perPage, ['*'], 'page', $page);
        } else {
            $tinhs = DiemLayNuoc::query()->with('tinhThanh', 'quanHuyen')->get();
        }

        return response()->json([
            'data' => $tinhs,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $data['search'] = convert_vi_to_en($request->ten.$request->dia_chi);
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'dia_chi' => 'required',
            'tinh_thanh_id' => 'required',
            'quan_huyen_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Chưa nhập đủ thông tin, không thể cập nhật'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $data['ma'] = trim($data['ma']);
            if (DiemLayNuoc::where('ma','ilike', $data['ma'])->where('id', '<>',$id)->first()){
                return response(['message' => 'Mã điểm lấy nước đã tồn tại'], 400);
            }
            DiemLayNuoc::where('id', $id)->first()->update([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'dia_chi' => $data['dia_chi'],
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'quan_huyen_id' => $data['quan_huyen_id'],
                'don_vi_quan_ly' => $data['don_vi_quan_ly'],
                'loai' => $data['loai'],
                'description' => $data['description'],
                'status' => $data['status'],
                'important' => $data['important'],
                'lat' => $data['lat'],
                'long' => $data['long'],
                'don_vi_quan_ly_id' => $data['don_vi_quan_ly_id'],
                'kha_nang_cap_nuoc_cho_xe' => $data['kha_nang_cap_nuoc_cho_xe'],
                'search' => $data['search'],
                'hien_thi_tren_map' => $data['hien_thi_tren_map']
            ]);

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

    public function delete($id)
    {
        try {
            DiemLayNuoc::find($id)->delete();

            return response()->json([
                'message' => 'Xóa thành công',
                'code' => 200,
                'data' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể xóa điểm lấy nước này',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = DiemLayNuoc::where('id', $id)->first();

            return response()->json([
                'message' => 'lấy dữ liệu thành công',
                'code' => 200,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi, ',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }
}
