<?php

namespace App\Http\Controllers;

use App\DonDatHang;
use App\HangTonKho;
use App\KhachHang;
use App\NopTien;
use App\PhieuNhapKho;
use App\SanPhamDonDatHang;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
// use DB;
use Illuminate\Support\Facades\DB;

class DonDatHangController extends Controller
{
    public function addDonDatHang(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'ma' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Thiếu dữ liệu, không thể đặt hàng'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        if ($data['trang_thai'] == 'hoa_don') {
            $data['da_thanh_toan'] = $data['tong_tien'] -  $data['giam_gia'];
            $data['con_phai_thanh_toan'] = 0;
        }
        $khacHang = null;
        if (isset($data['khach_hang_id'])) {
            $khacHang = KhachHang::where('user_id', $data['khach_hang_id'])->first();
        }
        if (!$khacHang && $data['thanh_toan'] == 'tai_khoan') {
            return response(['message' => 'Tài khoản không tồn tại'], 500);
        }
        try {
            DB::beginTransaction();
            if ($khacHang && $data['thanh_toan'] == 'tai_khoan' && $khacHang->so_du < $data['con_phai_thanh_toan']) {
                return response(['message' => 'Số dư tài khoản không đủ'], 500);
            }
            $donHang = DonDatHang::create([
                'ma' => $data['ma'],
                'tong_tien' => $data['tong_tien'],
                'ten' => $data['ten'],
                'user_id' => $data['khach_hang_id'],
                'ghi_chu' => $data['ghi_chu'],
                'giam_gia' => $data['giam_gia'],
                'bang_gia_id' => $data['bang_gia_id'],
                'da_thanh_toan' => $data['da_thanh_toan'],
                'trang_thai' => $data['trang_thai'],
                'con_phai_thanh_toan' => $data['con_phai_thanh_toan'],
                'thanh_toan' => $data['thanh_toan'],

            ]);
            foreach ($data['danhSachHang'] as $item) {
                if ($data['trang_thai'] == 'hoa_don') {
                    $tonKho = HangTonKho::where('san_pham_id', $item['hang_hoa']['id'])->first();
                    if (!$tonKho || $tonKho->so_luong < $item['so_luong']) {
                        DB::rollback();
                        return response(['message' => $item['hang_hoa']['ten_san_pham'] . ' Vượt quá số lượng tồn kho'], 500);
                    }
                    $tonKho->update(['so_luong' => $tonKho->so_luong - $item['so_luong']]);
                }
                SanPhamDonDatHang::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'gia_ban' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_dat_hang_id' => $donHang->id,
                    'doanh_thu' =>  $item['don_gia'] * $item['so_luong']
                ]);
            }
            if ($khacHang && $data['thanh_toan'] == 'tai_khoan') {
                $khacHang->update(['so_du' =>  $khacHang->so_du - ($donHang->tong_tien - $donHang->giam_gia)]);
                NopTien::create([
                    'trang_thai' => 'mua_hang',
                    'noi_dung' => 'Giao dịch mua hàng, đơn hàng: ' . $donHang->ten . ', mã đơn hàng: ' . $donHang->ma,
                    'id_user_khach_hang' => $khacHang->user_id,
                    'user_id' => $user->id,
                    'so_tien' => 0 - ($donHang->tong_tien - $donHang->giam_gia),
                    'so_du' => $khacHang->so_du - ($donHang->tong_tien - $donHang->giam_gia),
                    'ma' => 'GD' . time()
                ]);
            }
            DB::commit();
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể đặt hàng'], 500);
        }
    }

    public function getDonHang(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = DonDatHang::with('user', 'sanPhams', 'sanPhams.sanPham:id,ten_san_pham,don_vi_tinh');
        $date = $request->get('date');
        $khach_hang = $request->get('khach_hang');
        $hoaDon = $request->get('hoa_don');
        $trahang = $request->get('tra_hang');
        $don_hang = $request->get('don_hang');
        $donHang = [];
        if (isset($khach_hang)) {
            $query = $query->where('user_id', $khach_hang);
        }
        if ($hoaDon) {
            $query = $query->where('trang_thai', 'hoa_don');
        }
        if ($don_hang) {
            $query = $query->whereIn('trang_thai', ['moi_tao', 'huy_bo']);
        }
        if ($trahang) {
            $query = $query->where('trang_thai', 'huy_hoa_don');
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
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

    public function xoaDonHang($id)
    {
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            $donHang = DonDatHang::where('id', $id)->first();
            if ($donHang->trang_thai == 'hoa_don') {
                return response(['message' => 'Không thể xóa đơn đặt hàng đã chuyển hóa đơn'], 500);
            }
            $donHang->delete();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa đơn đặt hàng này'], 500);
        }
    }

    public function getChiTietDonDatHang($id)
    {
        $donHang = DonDatHang::with('sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        return response(['data' => $donHang], 200);
    }

    public function updateDonDatHang($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'ma' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Thiếu dữ liệu, không thể đặt hàng'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        if ($data['trang_thai'] == 'hoa_don') {
            $data['da_thanh_toan'] = $data['tong_tien'] -  $data['giam_gia'];
            $data['con_phai_thanh_toan'] = 0;
        }
        $khacHang = null;
        if (isset($data['khach_hang_id'])) {
            $khacHang = KhachHang::where('user_id', $data['khach_hang_id'])->first();
        }
        if (!$khacHang && $data['thanh_toan'] == 'tai_khoan') {
            return response(['message' => 'Tài khoản không tồn tại'], 500);
        }
        try {
            DB::beginTransaction();
            if ($khacHang && $data['thanh_toan'] == 'tai_khoan' && $khacHang->so_du < $data['con_phai_thanh_toan']) {
                return response(['message' => 'Số dư tài khoản không đủ'], 500);
            }
            $donHang = DonDatHang::where('id', $id)->first()->update([
                'ma' => $data['ma'],
                'tong_tien' => $data['tong_tien'],
                'ten' => $data['ten'],
                'user_id' => $data['khach_hang_id'],
                'ghi_chu' => $data['ghi_chu'],
                'giam_gia' => $data['giam_gia'],
                'da_thanh_toan' => $data['da_thanh_toan'],
                'trang_thai' => $data['trang_thai'],
                'con_phai_thanh_toan' => $data['con_phai_thanh_toan'],
                'bang_gia_id' => $data['bang_gia_id'],
                'thanh_toan' => $data['thanh_toan'],

            ]);
            foreach ($data['danhSachHang'] as $item) {
                if ($data['trang_thai'] == 'hoa_don') {
                    $tonKho = HangTonKho::where('san_pham_id', $item['hang_hoa']['id'])->first();
                    if (!$tonKho || $tonKho->so_luong < $item['so_luong']) {
                        DB::rollback();
                        return response(['message' => $item['hang_hoa']['ten_san_pham'] . ' Vượt quá số lượng tồn kho'], 500);
                        break;
                    }
                    $tonKho->update(['so_luong' => $tonKho->so_luong - $item['so_luong']]);
                }
            }

            SanPhamDonDatHang::where('don_dat_hang_id', $id)->delete();
            foreach ($data['danhSachHang'] as $item) {
                SanPhamDonDatHang::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'gia_ban' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_dat_hang_id' => $id
                ]);
            }
            if ($khacHang && $data['thanh_toan'] == 'tai_khoan') {
                $khacHang->update(['so_du' =>  $khacHang->so_du - ($donHang->tong_tien - $donHang->giam_gia)]);
                NopTien::create([
                    'trang_thai' => 'mua_hang',
                    'noi_dung' => 'Giao dịch mua hàng, đơn hàng: ' . $donHang->ten . ', mã đơn hàng: ' . $donHang->ma,
                    'id_user_khach_hang' => $khacHang->user_id,
                    'user_id' => $user->id,
                    'so_tien' => 0 - ($donHang->tong_tien - $donHang->giam_gia),
                    'so_du' => $khacHang->so_du - ($donHang->tong_tien - $donHang->giam_gia),
                    'ma' => 'GD' . time()
                ]);
            }
            DB::commit();
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return response(['message' => 'Không thể cập nhật'], 500);
        }
    }
    public function huyDon($id)
    {
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            DB::beginTransaction();
            $donHang = DonDatHang::where('id', $id)->first();
            $sanPham = SanPhamDonDatHang::where('don_dat_hang_id', $id)->get();
            if ($donHang->trang_thai == 'hoa_don') {
                $donHang->update(['trang_thai' => 'huy_hoa_don']);
                foreach ($sanPham as $item) {
                    $tonKho = HangTonKho::where('san_pham_id', $item['san_pham_id'])->first();
                    if ($tonKho)
                        $tonKho->update(['so_luong' => $tonKho->so_luong + $item['so_luong']]);
                }
                PhieuNhapKho::create(['don_hang_id' => null, 'ma' => 'PNK' . $id, 'user_id' => $user->id, 'kho_id' => null]);
                $khachHang = KhachHang::where('user_id', $donHang->user_id)->first();
                if ($khachHang) {
                    NopTien::create([
                        'trang_thai' => 'hoan_tien',
                        'id_user_khach_hang' => $donHang->user_id,
                        'user_id' => $user->id,
                        'so_tien' =>  $donHang->da_thanh_toan,
                        'noi_dung' => 'Hoàn tiền đơn hàng: ' . $donHang->ten . ', mã đơn hàng: ' . $donHang->ma,
                        'so_du' => $khachHang->so_du + $donHang->da_thanh_toan,
                        'ma' => 'GD' . time()
                    ]);
                    $khachHang->update(['so_du' => $khachHang->so_du + $donHang->da_thanh_toan]);
                }
            } else {
                $donHang->update(['trang_thai' => 'huy_bo']);
            }
            DB::commit();
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể hủy đơn'], 500);
        }
    }
    public function chuyenHoaDon($id, Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $validator = Validator::make($data, [
            'thanh_toan' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Thiếu dữ liệu, không thể đặt hàng'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        if (!$data['khach_hang_id'] && $data['thanh_toan'] == 'tai_khoan') {
            return response(['message' => 'Tài khoản không tồn tại'], 500);
        }
        $khacHang = KhachHang::where('user_id', $data['khach_hang_id'])->first();
        if (!$khacHang && $data['thanh_toan'] == 'tai_khoan') {
            return response(['message' => 'Tài khoản không tồn tại'], 500);
        }
        try {
            DB::beginTransaction();
            $donHang = DonDatHang::where('id', $id)->first();
            if ($khacHang && $data['thanh_toan'] == 'tai_khoan' && $khacHang->so_du < ($donHang->tong_tien - $donHang->giam_gia)) {
                return response(['message' => 'Số dư tài khoản không đủ'], 500);
            }
            if ($khacHang && $data['thanh_toan'] == 'tai_khoan') {
                $khacHang->update(['so_du' =>  $khacHang->so_du - ($donHang->tong_tien - $donHang->giam_gia)]);
                NopTien::create([
                    'trang_thai' => 'mua_hang',
                    'noi_dung' => 'Giao dịch mua hàng, đơn hàng: ' . $donHang->ten . ', mã đơn hàng: ' . $donHang->ma,
                    'id_user_khach_hang' => $khacHang->user_id,
                    'user_id' => $user->id,
                    'so_tien' => 0 - ($donHang->tong_tien - $donHang->giam_gia),
                    'so_du' => $khacHang->so_du,
                    'ma' => 'GD' . time()
                ]);
            }
            $donHang->update([
                'thanh_toan' => $data['thanh_toan'],
                'trang_thai' => 'hoa_don',
                'nhan_vien_giao_hang' => $data['nhan_vien_giao_hang'],
                'da_thanh_toan' => $donHang->tong_tien - $donHang->giam_gia,
                'con_phai_thanh_toan' => 0
            ]);
            $sanPhams = SanPhamDonDatHang::where('don_dat_hang_id', $id)->get();
            foreach($sanPhams as $item){
                SanPhamDonDatHang::where('id', $item->id)->first()->update([
                    'doanh_thu' => $item->gia_ban * $item->so_luong
                ]);
            }
            DB::commit();
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể chuyển hóa đơn'], 500);
        }
    }
}
