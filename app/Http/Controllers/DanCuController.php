<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\DanCu;
use App\ToaNha;

class DanCuController extends Controller
{
    public function getDanCu(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $user = auth()->user();
        $query = DanCu::with('toaNha', 'tinhThanh', 'donViPccc');
        $search = $request->get('search');
        $toa_nha_id = $request->get('toa_nha_id');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $don_vi_pccc_id = $request->get('don_vi_pccc_id');
        if (isset($toa_nha_id)) {
            $query->where('toa_nha_id', $toa_nha_id);
        }
        if (isset($tinh_thanh_id)) {
            $query->where('tinh_thanh_id', $tinh_thanh_id);
        }
        if (isset($don_vi_pccc_id)) {
            $query->where('don_vi_pccc_id', $don_vi_pccc_id);
        }
        if (isset($search)) {
            $search = trim($search);
            $query->where('name', 'ilike', "%{$search}%");
            $query->orWhere('phone', 'ilike', "%{$search}%");
            $query->orWhere('search', 'ilike', "%{$search}%");
            $query->orWhereHas('toaNha', function ($query) use ($search) {
                $query->where('ten', 'ilike', $search);
            });
        }

        // $query->with('toaNha', 'tinhThanh', 'donViPccc');
        $query->orderBy('updated_at', 'desc');
        $dancu = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $dancu,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function addDanCu(Request $request)
    {
        $data = $request->all();
        $data['search'] = convert_vi_to_en($request->name);
        $validator = Validator::make($data, [
            'name' => 'required',
        ]);
        $user = auth()->user();

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm dân cư'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            if (!empty($user->tinh_thanh_id)) {
                $data['tinh_thanh_id'] = $user->tinh_thanh_id;
            }
            $dancu = DanCu::create($data);

            return response()->json([
                'message' => 'Thành công',
                'data' => $dancu,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể thêm dân cư',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function editDanCu(Request $request, $id)
    {
        $data = $request->only('name', 'phone', 'toa_nha_id', 'tinh_thanh_id', 'don_vi_pccc_id');
        $data['search'] = convert_vi_to_en($request->name);
        $validator = Validator::make($data, [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Thiếu thông tin'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            DanCu::where('id', $id)->first()->update($data);

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

    public function deleteDanCu(Request $request, $id)
    {
        try {
            DanCu::find($id)->delete();

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

    public function search(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $query = ToaNha::query();
        $toa_nha = $request->get('query1');
        $tinh_thanh = $request->get('query2');
        $don_vi_pccc = $request->get('query3');
        if (!empty($tinh_thanh)) {
            $query->where('tinh_thanh_id', $tinh_thanh);
            if (!empty($don_vi_pccc)) {
                $query->where('don_vi_pccc_id', $don_vi_pccc);
            }
        } else {
            if (!empty($don_vi_pccc)) {
                $query->where('don_vi_pccc_id', $don_vi_pccc);
            }
        }
        if (!empty($toa_nha)) {
            $query->where('ten', 'ilike', "%{$toa_nha}%");
            $data = $query->get();
        } else {
            $data = [];
        }

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
}
