<?php

namespace App\Http\Controllers;

use App\CuuHoCuuNan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;

class CuuHoCuuNanController extends Controller
{
    public function create(Request $request)
    {
        $user = auth()->user();
        $info = $request->all();
        if (isset($user->tinh_thanh_id)) {
            $info['tinh_thanh_id'] = $user->tinh_thanh_id;
        }
        if (isset($info['thoi_gian_bat_dau_xu_ly'])) $info['thoi_gian_bat_dau_xu_ly'] = Carbon::parse($info['thoi_gian_bat_dau_xu_ly'])->timezone('Asia/Ho_Chi_Minh');
        if (isset($info['thoi_gian_ket_thuc'])) $info['thoi_gian_ket_thuc'] = Carbon::parse($info['thoi_gian_ket_thuc'])->timezone('Asia/Ho_Chi_Minh');
        if (isset($info['thoi_gian_nhan_tin_bao'])) $info['thoi_gian_nhan_tin_bao'] = Carbon::parse($info['thoi_gian_nhan_tin_bao'])->timezone('Asia/Ho_Chi_Minh');

        $validator = Validator::make($info, [
            'ten' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'tinh_thanh_id'  => 'required',
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
            $toanha = CuuHoCuuNan::create($info);
            return response()->json([
                'message' => 'Thành công',
                'data' => $toanha,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể thêm mới',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $user = auth()->user();
        $query = CuuHoCuuNan::query()->with(['tinhThanh', 'quanHuyen']);
        $search = $request->get('search');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $quan_huyen_id = $request->get('quan_huyen_id');
        if($user->role_id == 2 && $user->tinh_thanh_id){
            $tinh_thanh_id = $user->tinh_thanh_id;
        };
        $date = $request->get('date');
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('dia_chi', 'ilike', "%{$search}%");
                $query->orWhere('ten', 'ilike', "%{$search}%");
                $query->orWhere('tom_tat_vu_viec_qua_trinh', 'ilike', "%{$search}%");
            });
        }
        if (isset($date)) {
            $query->whereHas('thietBi', function ($query) use ($date) {
                $query->where('thoi_gian_nhan_tin_bao', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                    ->where('thoi_gian_nhan_tin_bao', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
            });
        }
        if (isset($tinh_thanh_id)) {
            $query->where('tinh_thanh_id', $tinh_thanh_id);
        }
        if (isset($quan_huyen_id)) {
            $query->where('quan_huyen_id', $quan_huyen_id);
        }
        $query->orderBy('updated_at', 'desc');
        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
        ], 200);
    }

    public function show($id)
    {
        $donvi = CuuHoCuuNan::with(['tinhThanh', 'quanHuyen'])->find($id);

        return response()->json([
            'data' => $donvi,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function update($id, Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'tinh_thanh_id'  => 'required',
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
            CuuHoCuuNan::where('id', $id)->first()->update([
                "tinh_thanh_id" => $data['tinh_thanh_id'],
                "quan_huyen_id" => $data['quan_huyen_id'],
                "dia_chi" => $data['dia_chi'],
                "ten" => $data['ten'],
                "noi_xay_ra" => $data['noi_xay_ra'],
                "khu_vuc" => $data['khu_vuc'],
                "nguoi_bao" => $data['nguoi_bao'],
                "so_dien_thoai_nguoi_bao" => $data['so_dien_thoai_nguoi_bao'],
                "lat" => $data['lat'],
                "long" => $data['long'],
                "thoi_gian_nhan_tin_bao" => $data['thoi_gian_nhan_tin_bao'],
                "thoi_gian_bat_dau_xu_ly" => $data['thoi_gian_bat_dau_xu_ly'],
                "thoi_gian_ket_thuc" => $data['thoi_gian_ket_thuc'],
                "nguyen_nhan" => $data['nguyen_nhan'],
                "so_nguoi_tham_gia" => $data['so_nguoi_tham_gia'],
                "so_nguoi_chet" => $data['so_nguoi_chet'],
                "so_nguoi_duoc_cuu_truc_tiep" => $data['so_nguoi_duoc_cuu_truc_tiep'],
                "so_nguoi_tu_thoat" => $data['so_nguoi_tu_thoat'],
                "thiet_hai_tai_san" => $data['thiet_hai_tai_san'],
                "hau_qua_khac" => $data['hau_qua_khac'],
                "tom_tat_vu_viec_qua_trinh" => $data['tom_tat_vu_viec_qua_trinh'],
                "khoang_cach_doi_cc" => $data['khoang_cach_doi_cc'],
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
        CuuHoCuuNan::find($id)->delete();
        return response()->json([
            'message' => 'thành công',
            'code' => 200,
        ], 200);
    }
}
