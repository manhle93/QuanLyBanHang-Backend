<?php

namespace App\Http\Controllers;

use App\HomNayAnGi;
use App\MonNgonMoiNgay;
use App\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomNayAnGiController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->all();
        $validater = Validator::make($data, [
            'ten' => 'required',
            'nguyen_lieus' => 'required'
        ]);
        if ($validater->fails()) {
            return response(['message' => 'Thiếu dữ liệu!'], 400);
        }
        try {
            if (!count($data['nguyen_lieus'])) {
                return response(['message' => 'Thiếu nguyên liệu. Không thể thêm món ăn!'], 400);
            }
            HomNayAnGi::create([
                'ten' => $data['ten'],
                'so_nguoi_an' => $data['so_nguoi_an'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'mo_ta' => $data['mo_ta'],
                'nguyen_lieu' => json_encode($data['nguyen_lieus'])
            ]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm món ăn'], 500);
        }
    }
    public function edit(Request $request)
    {
        $data = $request->all();
        $validater = Validator::make($data, [
            'ten' => 'required',
            'nguyen_lieus' => 'required'
        ]);
        if ($validater->fails()) {
            return response(['message' => 'Thiếu dữ liệu!'], 400);
        }
        try {
            if (!count($data['nguyen_lieus'])) {
                return response(['message' => 'Thiếu nguyên liệu. Không thể thêm món ăn!'], 400);
            }
            HomNayAnGi::where('id', $data['id'])->update([
                'ten' => $data['ten'],
                'so_nguoi_an' => $data['so_nguoi_an'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'mo_ta' => $data['mo_ta'],
                'nguyen_lieu' => json_encode($data['nguyen_lieus'])
            ]);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật món ăn'], 500);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = HomNayAnGi::query();
        $search = $request->get('search');

        if (isset($search)) {
            $search = trim($search);
            $query->where('ten', 'ilike', "%{$search}%");
            $query->orWhere('mo_ta', 'ilike', "%{$search}%");
        }

        $query->orderBy('updated_at', 'desc');
        $dancu = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $dancu,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function delete($id){
        try{
            HomNayAnGi::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Không xóa thêm món ăn'], 500);
        }
    }

    public function hienThiTrangChu(Request $request){
        $data = $request->all();
        $validater = Validator::make($data, [
            'id' => 'required',
            'hien_thi' => 'required'
        ]);
        if ($validater->fails()) {
            return response(['message' => 'Thiếu dữ liệu!'], 400);
        }
        try{
            HomNayAnGi::where('id', $data['id'])->update([
                'hien_thi' => $data['hien_thi']
            ]);
            return response(['message' => 'Thành công'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Không thể cập nhật'], 500);
        }
    }

    public function getSanPhamTrangChu(Request $request){
        return HomNayAnGi::where('hien_thi', true)->get();
    }

    public function chiTietMonAn($id){
        $data = HomNayAnGi::where('hien_thi', true)->where('id', $id)->first();
        $nguyen_lieu = collect(json_decode($data['nguyen_lieu']));
        foreach($nguyen_lieu as $item){
            $sanPham = SanPham::where('id', $item->nguyen_lieu_id)->with('danhMuc:id,kinh_doanh', 'sanPhamTonKho:san_pham_id,so_luong')->first();
            $item->kinh_doanh= $sanPham->danhMuc ? $sanPham->danhMuc->kinh_doanh : false;
            $item->gia_ban= $sanPham->gia_ban;
            $item->hinh_anh = $sanPham->anh_dai_dien;
            $item->ton_kho = $sanPham->sanPhamTonKho ? $sanPham->sanPhamTonKho->so_luong : 0;
        }
        // $nguyenLieu = SanPham::with('danhMuc')->whereIn('id', $nguyen_lieu_id)->get();
        return response(['san_pham' => $data, 'nguyen_lieu' => $nguyen_lieu],200);
    }
}
