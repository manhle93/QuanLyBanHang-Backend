<?php

namespace App\Http\Controllers;

use App\CanBoChienSi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;


class CanBoChienSiController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'tinh_thanh_id'  => 'required',
            'cmnd'  => 'required'

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
        if ($data['truc_thuoc_quan_huyen']) $data['don_vi_pccc_id'] = null;
        if (!$data['truc_thuoc_quan_huyen']) $data['quan_huyen_id'] = null;
        if(CanBoChienSi::where('cmnd', $data['cmnd'])->first()){
            return response(['message' => 'Số chứng minh đã tồn tại'], 402);
        }
        try {
            $canBoChienSi = CanBoChienSi::create([
                'ten' => $data['ten'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'ngay_sinh' => $data['ngay_sinh'],
                'don_vi_pccc_id' => $data['don_vi_pccc_id'],
                'loai_nhan_su' => $data['loai_nhan_su'],
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'quan_huyen_id' => $data['quan_huyen_id'],
                'cap_bac_id' => $data['cap_bac_id'],
                'chuc_vu_id' => $data['chuc_vu_id'],
                'cmnd' => $data['cmnd'],
            ]);
            return response('created', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm mới', 'data' => $e], 500);
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = [];
        if(!$user){
            return;
        }
        if ($user->role_id == 1) {
            $query = CanBoChienSi::query();
        }
        if ($user->role_id == 2 && $user->tinh_thanh_id != null) {
            $query = CanBoChienSi::where('tinh_thanh_id', $user->tinh_thanh_id);
        }
        if(!$user){
            return;
        }
        $search = $request->get('search');
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $tinhThanh = $request->get('tinh_thanh_id');
        $quanHuyen = $request->get('quan_huyen_id');
        $donVi = $request->get('don_vi_pccc_id');

        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('ten', 'ilike', "%{$search}%")
                    ->orWhere('so_dien_thoai', 'ilike', "%{$search}%");
            });

            // $query->orWhere('so_dien_thoai', 'ilike', "%{$search}%");
        }
        if (isset($tinhThanh)) {
            $query->where('tinh_thanh_id', $tinhThanh);
        }
        if (isset($quanHuyen)) {
            $query->where('quan_huyen_id', $quanHuyen);
        }
        if (isset($donVi)) {
            $query->where('don_vi_pccc_id', $donVi);
        }
        $query->with(['quanHuyen', 'donViPccc', 'capBac', 'chucVu']);
        $query->orderBy('created_at', 'DESC');
        // $query->join('cap_bacs', 'cap_bacs.id', '=', 'can_bo_chien_sis.cap_bac_id')->orderBy('cap_bacs.level');

        $xe = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $xe,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        $data = $request->only('ten', 'so_dien_thoai', 'quan_huyen_id', 'ngay_sinh', 'tinh_thanh_id', 'don_vi_pccc_id', 'loai_nhan_su', 'cap_bac_id', 'chuc_vu_id', 'cmnd');
        $validator = Validator::make($data, [
            'ten' => 'required',
            'tinh_thanh_id'  => 'required',
            'cmnd'  => 'required'

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
        if(CanBoChienSi::where('cmnd', $data['cmnd'])->where('id', '<>', $id)->first()){
            return response(['message' => 'Số chứng minh đã tồn tại'],500);
        }
        try {
            CanBoChienSi::where('id', $id)->first()->update($data);
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


    public function destroy($id)
    {
        try {
            CanBoChienSi::find($id)->delete();
            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response(['message' => 'Không thể xóa nhân sự này'], 400);
        }
    }
}
