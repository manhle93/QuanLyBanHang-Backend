<?php

namespace App\Http\Controllers;

use App\BaoGia;
use App\DonDatHang;
use App\DonHangNhaCungCap;
use App\PhieuThu;
use App\SanPham;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function getSanPham(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = SanPham::with('danhMuc');
        $search = $request->get('search');
        $danh_muc_id = $request->get('danh_muc_id');
        if (isset($danh_muc_id)) {
            $query->where('danh_muc_id', $danh_muc_id);
        }
        if (isset($search)) {
            $search = trim($search);
            $query->where('ten_san_pham', 'ilike', "%{$search}%");
            $query->orWhere('mo_ta_san_pham', 'ilike', "%{$search}%");
        }

        $query->orderBy('updated_at', 'desc');
        $dancu = $query->paginate($perPage, ['*'], 'page', $page);

        return $dancu;
    }
    public function getDonDatHang(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $date = $request->get('date');
        $nhac_cung_cap = $request->get('nha_cung_cap');
        $query = DonHangNhaCungCap::with('user:id,name', 'sanPhams');
        $donHang = [];
        if (isset($nhac_cung_cap)) {
            $query = $query->where('user_id', $nhac_cung_cap);
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if ($user->role_id == 1 || $user->role_id == 2) {
            $donHang = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        if ($user->role_id == 3) {
            $donHang = $query->where('user_id', $user->id)->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        return $donHang;
    }
    public function getChiTietDonHang($id)
    {
        $donHang = DonHangNhaCungCap::where('id', $id)->with('sanPhams', 'sanPhams.sanPham')->first();
        return $donHang;
    }

    public function getChiTietBaoGia($id)
    {
        $donHang = BaoGia::with('user', 'sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        return $donHang;
    }
    public function getBaoGia(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = BaoGia::with('user');
        $nha_cung_cap = $request->get('nha_cung_cap');
        $date = $request->get('date');
        $data = [];
        if (isset($search)) {
            $search = trim($search);
            $query->where('ten', 'ilike', "%{$search}%");
        }
        if (isset($nha_cung_cap)) {
            $query = $query->where('user_id', $nha_cung_cap);
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if ($user->role_id == 3) {
            $data = $query->where('user_id', $user->id)->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        if ($user->role_id == 1 || $user->role_id == 2) {
            $data = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }

        return $data;
    }
    public function me()
    {
        $user = auth()->user();
        $data = User::where('id', $user->id)->with('nhaCungCap')->first();
        return $data;
    }

    public function getPhieuThu(Request $request){

        $user = auth()->user();
        if(!$user){
            return [];
        }
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = PhieuThu::where('user_id_khach_hang', $user->id);
        $date = $request->get('date');
        $data = [];
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        $data = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }
}
