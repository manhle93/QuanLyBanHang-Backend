<?php

namespace App\Http\Controllers;

use App\DonViPccc;
use App\PhuongTienPccc;
use App\TinhThanh;
use Illuminate\Http\Request;

class QuanLyChungController extends Controller
{
    public function getTinhThanh()
    {
        $tinh = TinhThanh::has('donViPccc')->with('donViPccc', 'quanHuyen')->select('id', 'name as ten')->get();
        return response($tinh, 200);
    }

    public function getPT(Request $request)
    {
        $user = auth()->user();
        $query = [];
        if(!$user){
            return;
        }
        if ($user->role_id == 1) {
            $query = PhuongTienPccc::query();
        }
        if ($user->role_id == 2 && $user->tinh_thanh_id != null) {
            $query = PhuongTienPccc::where('tinh_thanh_id', $user->tinh_thanh_id);
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
                $query->where('bien_so', 'ilike', "%{$search}%")
                ->orWhere('ten', 'ilike', "%{$search}%")
                ->orWhere('ten', 'ilike', "%{$search}%")
                ->orWhere('so_dien_thoai', 'ilike', "%{$search}%")
                ->orWhere('so_hieu', 'ilike', "%{$search}%")
                ->orWhere('imei', 'ilike', "%{$search}%");
            });
            
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
        $query->orderBy('updated_at', 'desc');
        $query->with(['quanHuyen', 'donViPccc', 'loaiPhuongTienPccc']);
        $xe = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $xe,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function getDV(Request $request)
    {
        $user = auth()->user();
        $donVi = [];
        if(!$user){
            return;
        }
        if ($user->role_id == 1) {
            $donVi = DonViPccc::where('hien_thi_tren_map', true)->get();
        }
        if ($user->role_id == 2 && $user->tinh_thanh_id != null) {
            $donVi = DonViPccc::where('hien_thi_tren_map', true)->where('tinh_thanh_id', $user->tinh_thanh_id)->get();
        }
        return response($donVi, 200);
    }

    public function getChiTietTinhThanh()
    {
        $tinh = [];
        $user = auth()->user();
        if(!$user){
            return;
        }
        if ($user->role_id == 2 && $user->tinh_thanh_id) {
            $tinh = TinhThanh::where('id', $user->tinh_thanh_id)->with('donViPccc', 'quanHuyen')->select('id', 'name as ten')->get();
        }
        if($user->role_id == 1){
            $tinh = TinhThanh::with('donViPccc', 'quanHuyen')->select('id','code', 'name as ten')->orderBy('rate', 'ASC')->get();
        }
        return response($tinh, 200);
    }
}
