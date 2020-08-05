<?php

namespace App\Http\Controllers;

use App\SanPham;
use App\SanPhamVoucher;
use App\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function addVoucher(Request $request)
    {

        $data = $request->all();
        $user = auth()->user();
        if(!$user || $user->role_id != 1){
            return response(['message' => 'Không có quyền'], 500);
        }
        $validator = Validator::make($data, [
            'ma' => 'required',
            'giam_gia' => 'required',
            'so_luong' => 'required',
            'thoi_gian' => 'required'
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
            if(Voucher::where('ma', $data['ma'])->first()){
                return response(['message' => 'Mã Voucher đã tồn tại'], 500);
            }
            if (count($data['thoi_gian']) != 2) {
                return response(['message' => 'Thời gian không hợp lệ'], 500);
            }
            $data['bat_dau'] = Carbon::parse($data['thoi_gian'][0]);
            $data['ket_thuc'] = Carbon::parse($data['thoi_gian'][1]);
            DB::beginTransaction();
            $voucher = Voucher::create([
                'ma' => $data['ma'],
                'so_luong' => $data['so_luong'],
                'ap_dung_cho' => $data['ap_dung_cho'],
                'don_toi_thieu' => $data['don_toi_thieu'],
                'bat_dau' => $data['bat_dau'],
                'ket_thuc' => $data['ket_thuc'],
                'loai' => $data['loai'],
                'giam_gia' => $data['giam_gia'],
                'mo_ta' => $data['mo_ta'],

            ]);
            $sanPhams = [];
            if ($data['ap_dung_cho'] == 'san_pham') {
                $sanPhams = $data['sanPhams'];
            }
            if ($data['ap_dung_cho'] == 'danh_muc') {
                $sanPhams = SanPham::whereIn('danh_muc_id', $data['danhMucs'])->pluck('id')->toArray();
            }
            if ($data['ap_dung_cho'] == 'toan_bo') {
                $sanPhams = SanPham::pluck('id')->toArray();
            }
            if(count($sanPhams) < 1){
                DB::rollBack();
                return response(['message' => 'Sản phẩm không tồn tại'], 500);
            }
            foreach ($sanPhams as $item) {
                SanPhamVoucher::create([
                    'voucher_id' => $voucher->id,
                    'san_pham_id' => $item
                ]);
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể tạo voucher'], 500);
        }
    }

    public function getVoucher(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = Voucher::with('sanPhams', 'sanPhams.sanPham:id,ten_san_pham,don_vi_tinh,danh_muc_id');
        $date = $request->get('date');
        $search = $request->get('search');
        $donHang = [];
        if(isset($search)){
            $query->where('ma', 'ilike', "%{$search}%")
            ->orWhere('mo_ta', 'ilike', "%{$search}%");
        }
        if (isset($date)) {
            $query->where('bat_dau', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('ket_thuc', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if ($user->role_id == 1 || $user->role_id == 2) {
            $donHang = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        }
        return response()->json([
            'data' => $donHang,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function updateVoucher($id, Request $request)
    {

        $data = $request->all();
        $user = auth()->user();
        if(!$user || $user->role_id != 1){
            return response(['message' => 'Không có quyền'], 500);
        }
        $validator = Validator::make($data, [
            'ma' => 'required',
            'giam_gia' => 'required',
            'so_luong' => 'required',
            'thoi_gian' => 'required'
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
            if(Voucher::where('ma', $data['ma'])->where('ma', '<>', $data['ma'])->first()){
                return response(['message' => 'Mã Voucher đã tồn tại'], 500);
            }

            if (count($data['thoi_gian']) != 2) {
                return response(['message' => 'Thời gian không hợp lệ'], 500);
            }
            $data['bat_dau'] = Carbon::parse($data['thoi_gian'][0]);
            $data['ket_thuc'] = Carbon::parse($data['thoi_gian'][1]);
            DB::beginTransaction();
            $voucher = Voucher::find($id)->update([
                'ma' => $data['ma'],
                'so_luong' => $data['so_luong'],
                'ap_dung_cho' => $data['ap_dung_cho'],
                'don_toi_thieu' => $data['don_toi_thieu'],
                'bat_dau' => $data['bat_dau'],
                'ket_thuc' => $data['ket_thuc'],
                'loai' => $data['loai'],
                'giam_gia' => $data['giam_gia'],
                'mo_ta' => $data['mo_ta'],

            ]);
            $sanPhams = [];
            if ($data['ap_dung_cho'] == 'san_pham') {
                $sanPhams = $data['sanPhams'];
            }
            if ($data['ap_dung_cho'] == 'danh_muc') {
                $sanPhams = SanPham::whereIn('danh_muc_id', $data['danhMucs'])->pluck('id')->toArray();
            }
            if ($data['ap_dung_cho'] == 'toan_bo') {
                $sanPhams = SanPham::pluck('id')->toArray();
            }
            if(count($sanPhams) < 1){
                DB::rollBack();
                return response(['message' => 'Sản phẩm không tồn tại'], 500);
            }
            SanPhamVoucher::where('voucher_id', $id)->delete();
            foreach ($sanPhams as $item) {
                SanPhamVoucher::create([
                    'voucher_id' => $id,
                    'san_pham_id' => $item
                ]);
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể cập nhật voucher'], 500);
        }
    }

    public function xoaVoucher($id){
        try{
            Voucher::find($id)->delete();
            SanPhamVoucher::where('voucher_id', $id)->delete();
            return response(['message' => 'Thành công'], 200);
        }catch(\Exception $e){
            return response(['message' => 'Không thể xóa cấu hình'], 500);
        }
    }
}
