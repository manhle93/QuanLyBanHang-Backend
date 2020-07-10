<?php

namespace App\Http\Controllers;

use App\DonHangNhaCungCap;
use App\HangTonKho;
use App\PhieuNhapKho;
use App\SanPham;
use App\SanPhamDonHangNhaCungCap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuanLyKhoController extends Controller
{
    public function getPhieuNhap(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập'], 401);
        }
        if ($user->role_id != 1 && $user->role_id != 2) {
            return response(['message' => 'Không có quyền'], 402);
        }
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $query = PhieuNhapKho::with('donHang', 'donHang.sanPhams', 'donHang.sanPhams.sanPham:id,ten_san_pham');
        if (isset($search)) {
            $query->where('ma', 'ilike', "%{{$search}}&");
        }
        $query->orderBy('created_at', 'desc');
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function hangTonKho()
    {
        $sanPham = SanPham::select('id', 'ten_san_pham')->get();
        $donHangNhapKho = DonHangNhaCungCap::where('trang_thai', 'nhap_kho')->pluck('id')->toArray();
        foreach ($sanPham as $item) {
            $soNhapKho = SanPhamDonHangNhaCungCap::where('san_pham_id', $item->id)->whereIn('don_hang_id', $donHangNhapKho)->sum('so_luong');
            $item['ton_kho'] = $soNhapKho;
        }
        return $sanPham;
    }

    public function getHangTonKho(Request $request)
    {

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = HangTonKho::with('sanPham', 'kho');
        $search = $request->get('search');
        $danh_muc_id = $request->get('danh_muc_id');
        if (isset($danh_muc_id)) {
            $query->whereHas('sanPham', function ($query) use ($danh_muc_id) {
                $query->where('danh_muc_id', $danh_muc_id);
            });
        }
        if (isset($search)) {
            $search = trim($search);
            $query->whereHas('sanPham', function ($query) use ($search) {
                $query->where('ten_san_pham', 'ilike', "%{$search}%");
                $query->orWhere('mo_ta_san_pham', 'ilike', "%{$search}%");
            });
        }

        $query->orderBy('updated_at', 'desc');
        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
}
