<?php

namespace App\Http\Controllers;

use App\DinhMucSanXuat;
use App\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DinhMucSanXuatController extends Controller
{
    public function getSanPham(Request $request)
    {
        $search = $request->get('search');
        $query = SanPham::where('danh_muc_id', 38);
        if (isset($search)) {
            $search = trim($search);
            $query->whereRaw('CONCAT(unaccent(ten_san_pham), ten_san_pham) ilike ' . "'%{$search}%'");
            $query->orWhereRaw('CONCAT(unaccent(mo_ta_san_pham), mo_ta_san_pham) ilike ' . "'%{$search}%'");
        }
        $data = $query->take(20)->get();
        return $data;
    }
    public function getNguyenVatLieu(Request $request)
    {
        $search = $request->get('search');
        $query = SanPham::where('danh_muc_id', '<>', 38);
        if (isset($search)) {
            $search = trim($search);
            $query->whereRaw('CONCAT(unaccent(ten_san_pham), ten_san_pham) ilike ' . "'%{$search}%'");
            $query->orWhereRaw('CONCAT(unaccent(mo_ta_san_pham), mo_ta_san_pham) ilike ' . "'%{$search}%'");
        }
        $data = $query->take(20)->get();
        return $data;
    }

    public function themDinhMuc(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'san_pham_id' => 'required',
            'nguyen_lieus' => 'required'
        ]);
        if ($validate->fails()) {
            return response(['message' => 'Thiếu dữ liệu. Không thể tạo định mức!'], 400);
        }
        if (!count($data['nguyen_lieus'])) {
            return response(['message' => 'Thiếu nguyên liệu. Không thể tạo định mức!'], 400);
        }
        DB::beginTransaction();
        try {
            foreach ($data['nguyen_lieus'] as $item) {
                DinhMucSanXuat::create([
                    'san_pham_id' => $data['san_pham_id'],
                    'nguyen_lieu_id' => $item['nguyen_lieu_id'],
                    'so_luong' => $item['so_luong']
                ]);
            }
            DB::commit();
            return response(['message' => 'Done!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => ' Không thể tạo định mức!'], 500);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = SanPham::has('nguyenLieus')->with('nguyenLieus', 'nguyenLieus.nguyenLieus');
        $search = $request->get('search');

        if (isset($search)) {
            $search = trim($search);
            $query->whereHas('nguyenLieus', function ($query) use ($search) {
                $query->where('ten_san_pham', 'ilike', "%{$search}%");
                $query->orWhere('mo_ta_san_pham', 'ilike', "%{$search}%");
            });
        }

        $query->orderBy('updated_at', 'desc');
        $dancu = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $dancu,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function updateDinhMuc(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'san_pham_id' => 'required',
            'nguyen_lieus' => 'required'
        ]);
        if ($validate->fails()) {
            return response(['message' => 'Thiếu dữ liệu. Không thể tạo định mức!'], 400);
        }
        if (!count($data['nguyen_lieus'])) {
            return response(['message' => 'Thiếu nguyên liệu. Không thể tạo định mức!'], 400);
        }
        DB::beginTransaction();
        try {
            DinhMucSanXuat::where('san_pham_id', $data['san_pham_id'])->delete();
            foreach ($data['nguyen_lieus'] as $item) {
                DinhMucSanXuat::create([
                    'san_pham_id' => $data['san_pham_id'],
                    'nguyen_lieu_id' => $item['nguyen_lieu_id'],
                    'so_luong' => $item['so_luong']
                ]);
            }
            DB::commit();
            return response(['message' => 'Done!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => ' Không thể cập nhật định mức!'], 500);
        }
    }

    public function xoaDinhMuc(Request $request)
    {
        $san_pham_id = $request->san_pham_id;;
        if (!isset($san_pham_id) || !$san_pham_id) {
            return response(['message' => 'Sản phẩm không tồn tại!'], 400);
        }
        try {
            DinhMucSanXuat::where('san_pham_id', $san_pham_id)->delete();
            return response(['message' => 'Done!'], 200);
        } catch (\Exception $e) {
            return response(['message' => ' Không thể xóa định mức!'], 500);
        }
    }
}
