<?php

namespace App\Http\Controllers;

use App\PcccCoSoToaNha;
use App\PhuongTienToaNha;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class PhuongTienToaNhaController extends Controller
{
    public function addPcccCoSo(Request $request)
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
        if (isset($data['ngay_sinh'])) $data['ngay_sinh'] = Carbon::parse($data['ngay_sinh']);
        try {
            PcccCoSoToaNha::create([
                'ten' => $data['ten'],
                'toa_nha_id' => $data['toa_nha_id'],
                'ngay_sinh' => $data['ngay_sinh'],
                'bo_phan' => $data['bo_phan'],
                'dien_thoai' => $data['dien_thoai'],
                'duoc_cap_giay_cn' => $data['duoc_cap_giay_cn'],
                'trong_gio_lam_viec' => $data['trong_gio_lam_viec'],
                'doi_truong' => $data['doi_truong']
            ]);
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm PCCC cơ sở'], 500);
        }
    }

    public function updatePcccCoSo(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'id' => 'required'
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
        if (isset($data['ngay_sinh'])) $data['ngay_sinh'] = Carbon::parse($data['ngay_sinh']);
        try {
            PcccCoSoToaNha::where('id', $data['id'])->first()->update([
                'ten' => $data['ten'],
                'ngay_sinh' => $data['ngay_sinh'],
                'bo_phan' => $data['bo_phan'],
                'dien_thoai' => $data['dien_thoai'],
                'duoc_cap_giay_cn' => $data['duoc_cap_giay_cn'],
                'trong_gio_lam_viec' => $data['trong_gio_lam_viec'],
                'doi_truong' => $data['doi_truong']
            ]);
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật PCCC cơ sở'], 500);
        }
    }

    public function xoaPcccCoSo($id){
        try{
            PcccCoSoToaNha::find($id)->delete();
            return response(['message' => 'Cập nhật thành công'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Không thể cập nhật PCCC cơ sở'], 500);
        }
    }


    public function addPhuongtien(Request $request)
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
            PhuongTienToaNha::create([
                'ten' => $data['ten'],
                'toa_nha_id' => $data['toa_nha_id'],
                'loai' => $data['loai'],
                'loai_chi_tiet' => $data['loai_chi_tiet'],
                'so_luong' => $data['so_luong'],
                'tinh_trang' => $data['tinh_trang'],
                'vi_tri' => $data['vi_tri'],
            ]);
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm phương tiện'], 500);
        }
    }

    public function updatePhuongTien(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'id' => 'required'
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
            PhuongTienToaNha::where('id', $data['id'])->first()->update([
                'ten' => $data['ten'],
                'toa_nha_id' => $data['toa_nha_id'],
                'loai' => $data['loai'],
                'loai_chi_tiet' => $data['loai_chi_tiet'],
                'so_luong' => $data['so_luong'],
                'tinh_trang' => $data['tinh_trang'],
                'vi_tri' => $data['vi_tri'],
            ]);
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật phương tiện'], 500);
        }
    }

    public function xoaPhuongTien($id){
        try{
            PhuongTienToaNha::find($id)->delete();
            return response(['message' => 'Cập nhật thành công'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Không thể cập nhật PCCC cơ sở'], 500);
        }
    }
}
