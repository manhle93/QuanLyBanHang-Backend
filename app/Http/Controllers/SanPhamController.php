<?php

namespace App\Http\Controllers;

use App\HinhAnhSanPham;
use App\SanPham;
use Illuminate\Http\Request;
use Validator;

class SanPhamController extends Controller
{
    public function upload(Request $request)
    {
        $info = $request->all();
        $user = auth()->user();
        $validator = Validator::make($info, [
            'file' => 'required|file|max:32768',      // max 32MB = 32768KB,
        ]);

        if ($validator->fails()) {
            $message = 'validation failed';
            $failedRules = $validator->failed();

            if (isset($failedRules['file']['required'])) {
                $message = 'Tệp không được tìm thấy';
            } elseif (isset($failedRules['file']['file'])) {
                $message = 'Không hỗ trợ định dạng tệp';
            } elseif (isset($failedRules['file']['max'])) {
                $message = 'Kích thước tệp quá lớn';
            }

            return response()->json([
                'message' => $message,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $image = $request->file;
        $name = time() . '.' . $image->getClientOriginalExtension();
        $image->move('storage/images/avatar/', $name);
        $file = HinhAnhSanPham::create([
            'san_pham_id' => null,
            'url_hinh_anh' => 'storage/images/avatar/' . $name
        ]);
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'result' => $file->id,
        ]);
    }
    public function addSanPham(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'ten_san_pham' => 'required',
            'danh_muc_id' => 'required',
            'don_vi_tinh' => 'required',
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
        $files = $data['fileList'];
        try {
            $sanPham =  SanPham::create([
                'ten_san_pham' => $data['ten_san_pham'],
                'danh_muc_id' => $data['danh_muc_id'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'gia_ban' => $data['gia_ban'],
                'gia_sale' => $data['gia_sale'],
                'don_vi_tinh' => $data['don_vi_tinh'],
                'dang_khuyen_mai' => $data['dang_khuyen_mai'],
                'mo_ta_san_pham' => $data['mo_ta_san_pham'],
                'bat_dau_khuyen_mai' => $data['bat_dau_khuyen_mai'],
                'ket_thuc_khuyen_mai' => $data['ket_thuc_khuyen_mai'],
            ]);
            if (count($files) > 0) {
                foreach ($files as $item) {
                    if (!empty($item['response']['result'])) {
                        HinhAnhSanPham::where('id', $item['response']['result'])->update(['san_pham_id' => $sanPham->id]);
                    }
                }
            }
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm sản phẩm'], 500);
        }
    }

    public function getSanPham(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = SanPham::with('danhMuc');
        $search = $request->get('search');
        $danh_muc_id = $request->get('danh_muc_id');
        if (isset($toa_nha_id)) {
            $query->where('danh_muc_id', $danh_muc_id);
        }
        if (isset($search)) {
            $search = trim($search);
            $query->where('ten_san_pham', 'ilike', "%{$search}%");
            $query->orWhere('mo_ta_san_pham', 'ilike', "%{$search}%");
        }

        $query->orderBy('updated_at', 'desc');
        $dancu = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $dancu,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function xoaSanPham($id)
    {
        try {
            SanPham::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa sản phẩm này'], 500);
        }
    }

    public function getSanPhamDetail($id)
    {
        $sanPham = SanPham::where('id', $id)->with('hinhAnhs')->first();
        return response($sanPham, 200);
    }
    public function uploadEdit($id, Request $request)
    {
        $info = $request->all();
        $user = auth()->user();
        $validator = Validator::make($info, [
            'file' => 'required|file|max:32768',      // max 32MB = 32768KB,
        ]);

        if ($validator->fails()) {
            $message = 'validation failed';
            $failedRules = $validator->failed();

            if (isset($failedRules['file']['required'])) {
                $message = 'Tệp không được tìm thấy';
            } elseif (isset($failedRules['file']['file'])) {
                $message = 'Không hỗ trợ định dạng tệp';
            } elseif (isset($failedRules['file']['max'])) {
                $message = 'Kích thước tệp quá lớn';
            }

            return response()->json([
                'message' => $message,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $image = $request->file;
        $name = time() . '.' . $image->getClientOriginalExtension();
        $image->move('storage/images/avatar/', $name);
        $file = HinhAnhSanPham::create([
            'san_pham_id' => $id,
            'url_hinh_anh' => 'storage/images/avatar/' . $name
        ]);
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'result' => $file->id,
        ]);
    }

    public function xoaAnhSanPham(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return response()->json([
                'code' => 400,
                'message' => __('ID Hình ảnh không tồn tại'),
            ], 400);
        }
        try {
            HinhAnhSanPham::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa hình ảnh'], 500);
        }
    }

    public function editSanPham($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten_san_pham' => 'required',
            'danh_muc_id' => 'required',
            'don_vi_tinh' => 'required',
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
        $files = $data['fileList'];
        try {
            $sanPham =  SanPham::where('id', $id)->update([
                'ten_san_pham' => $data['ten_san_pham'],
                'danh_muc_id' => $data['danh_muc_id'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'gia_ban' => $data['gia_ban'],
                'gia_sale' => $data['gia_sale'],
                'don_vi_tinh' => $data['don_vi_tinh'],
                'dang_khuyen_mai' => $data['dang_khuyen_mai'],
                'mo_ta_san_pham' => $data['mo_ta_san_pham'],
                'bat_dau_khuyen_mai' => $data['bat_dau_khuyen_mai'],
                'ket_thuc_khuyen_mai' => $data['ket_thuc_khuyen_mai'],
            ]);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm sản phẩm'], 500);
        }
    }
}