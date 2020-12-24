<?php

namespace App\Http\Controllers;

use App\DoiTraHang;
use App\DonDatHang;
use App\HangTonKho;
use App\KhachHang;
use App\NopTien;
use App\PhieuNhapKho;
use App\PhieuThu;
use App\SanPhamDonDatHang;
use App\ThanhToanBoXung;
use Berkayk\OneSignal\OneSignalFacade;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
// use DB;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;

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
        if ($data['trang_thai'] == 'hoa_don' && $data['thanh_toan'] != 'tra_sau') {
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
                'phu_thu' => $data['trang_thai'] == 'hoa_don' ? $data['phu_thu'] : null,
                'thoi_gian_nhan_hang' => $data['thoi_gian_nhan_hang'],
                'dia_chi' => $data['dia_chi']

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
                if ($khacHang->so_du <  $data['con_phai_thanh_toan']) {
                    return response(['message' => 'Số dư tài khoản không đủ'], 500);
                }
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
            DB::commit();
            return response(['message' => 'Thêm mới thành công', 'don_hang_id' => $donHang->id], 200);
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
        $query = DonDatHang::with('user', 'sanPhams', 'sanPhams.sanPham:id,ten_san_pham,don_vi_tinh', 'thanhToanBoXung');
        $date = $request->get('date');
        $typeDate = $request->get('typeDate', 'tao_don');
        $khach_hang = $request->get('khach_hang');
        $hoaDon = $request->get('hoa_don');
        $trahang = $request->get('tra_hang');
        $don_hang = $request->get('don_hang');
        $search = $request->get('search');
        $donHang = [];
        if (isset($khach_hang)) {
            $query = $query->where('user_id', $khach_hang);
        }
        if ($hoaDon) {
            $query = $query->where('trang_thai', 'hoa_don');
        }
        if ($don_hang) {
            $query = $query->whereIn('trang_thai', ['moi_tao', 'huy_bo', 'khach_huy', 'dat_hang_online', 'mua_hang_online']);
        }
        if ($trahang) {
            $query = $query->where('trang_thai', 'huy_hoa_don');
        }
        if (isset($date)) {
            if ($typeDate == 'tao_don') {
                $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                    ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
            }
            if ($typeDate == 'nhan_hang') {
                $query->where('thoi_gian_nhan_hang', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                    ->where('thoi_gian_nhan_hang', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
            }
        }
        if (isset($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('ma', 'ilike', "%{$search}%")
                    ->orWhere('ten', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('phone', 'ilike', "%{$search}%")
                            ->orWhere('email', 'ilike', "%{$search}%")
                            ->orWhere('name', 'ilike', "%{$search}%");
                    });
            });
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
        if ($data['trang_thai'] == 'hoa_don' &&  $data['thanh_toan'] != 'tra_sau') {
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
            if ($khacHang && $data['thanh_toan'] == 'tai_khoan' && $khacHang->so_du < $data['con_phai_thanh_toan'] && $data['trang_thai'] == 'hoa_don') {
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
                'phu_thu' => $data['trang_thai'] == 'hoa_don' ? $data['phu_thu'] : null,
                'thoi_gian_nhan_hang' => $data['thoi_gian_nhan_hang'],
                'dia_chi' => $data['dia_chi']

            ]);
            foreach ($data['danhSachHang'] as $item) {
                if ($data['trang_thai'] == 'hoa_don' &&  count($data['doiTra']) == 0) {
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
                    'don_dat_hang_id' => $id,
                    'doanh_thu' => $item['don_gia'] * $item['so_luong']
                ]);
            }
            if ($khacHang && $data['thanh_toan'] == 'tai_khoan' && count($data['doiTra']) == 0) {
                if ($khacHang->so_du <  $data['con_phai_thanh_toan'] && $data['trang_thai'] == 'hoa_don') {
                    return response(['message' => 'Số dư tài khoản không đủ'], 500);
                }
                $khacHang->update(['so_du' => (float) $khacHang->so_du - ((float)$data['tong_tien'] - (float) $data['giam_gia'])]);
                NopTien::create([
                    'trang_thai' => 'mua_hang',
                    'noi_dung' => 'Giao dịch mua hàng, đơn hàng: ' . $data['ten'] . ', mã đơn hàng: ' . $data['ma'],
                    'id_user_khach_hang' => $khacHang->user_id,
                    'user_id' => $user->id,
                    'so_tien' => 0 - ((float)$data['tong_tien'] - (float) $data['giam_gia']),
                    'so_du' => (float) $khacHang->so_du,
                    'ma' => 'GD' . time()
                ]);
            }
            if ($khacHang && $data['thanh_toan'] == 'tai_khoan' && $data['trang_thai'] == 'hoa_don') {
                if ($khacHang->so_du <  $data['chenhLech']  && $data['trang_thai'] == 'hoa_don') {
                    return response(['message' => 'Số dư tài khoản không đủ'], 500);
                }
                $khacHang->update(['so_du' => (float) $khacHang->so_du - ((float)$data['chenhLech'])]);
                NopTien::create([
                    'trang_thai' => 'mua_hang',
                    'noi_dung' => 'Giao dịch đổi trả hàng, đơn hàng: ' . $data['ten'] . ', mã đơn hàng: ' . $data['ma'],
                    'id_user_khach_hang' => $khacHang->user_id,
                    'user_id' => $user->id,
                    'so_tien' => 0 - ((float)$data['tong_tien']),
                    'so_du' => (float) $khacHang->so_du,
                    'ma' => 'GD' . time()
                ]);
            }
            if (count($data['doiTra']) > 0) {
                foreach ($data['doiTra'] as $item) {
                    DoiTraHang::create([
                        'san_pham_id' => $item['hang_hoa']['id'],
                        'gia_ban' => $item['don_gia'],
                        'so_luong' => $item['so_luong'],
                        'don_hang_id' => $id,
                        'doanh_thu' => $item['don_gia'] * $item['so_luong']
                    ]);
                }
                $tonKho = HangTonKho::where('san_pham_id', $item['hang_hoa']['id'])->first();
                if ($tonKho) {
                    $tonKho->update(['so_luong' => $tonKho->so_luong + $item['so_luong']]);
                } else {
                    HangTonKho::create([
                        'san_pham_id' => $item['hang_hoa']['id'],
                        'so_luong' =>  $item['so_luong'],
                        'kho_id' => null
                    ]);
                }
            }
            DB::commit();
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
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
                PhieuNhapKho::create(['don_hang_id' => null,'don_dat_hang_id' => $id, 'ma' => 'PNK' . $id, 'user_id' => $user->id, 'kho_id' => null]);
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
            foreach ($sanPhams as $item) {
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

    public function inHoaDon($id)
    {
        $donHang = DonDatHang::with('sanPhams', 'sanPhams.sanPham:id,ten_san_pham,don_vi_tinh', 'user:id,name')->where('id', $id)->first();
        $time = Carbon::parse($donHang->created_at);
        $date = $time->day;
        $month = $time->month;
        $year = $time->year;
        $tien = $this->convert_number_to_words($donHang->tong_tien);
        return view('hoadon', ['ngay' => $date, 'thang' => $month, 'nam' => $year, 'data' => $donHang, 'tien' => $tien]);
    }

    function convert_number_to_words($number)
    {

        $hyphen      = ' ';
        $conjunction = '  ';
        $separator   = ' ';
        $negative    = 'âm ';
        $decimal     = ' phẩy ';
        $dictionary  = array(
            0                   => 'Không',
            1                   => 'Một',
            2                   => 'Hai',
            3                   => 'Ba',
            4                   => 'Bốn',
            5                   => 'Năm',
            6                   => 'Sáu',
            7                   => 'Bảy',
            8                   => 'Tám',
            9                   => 'Chín',
            10                  => 'Mười',
            11                  => 'Mười một',
            12                  => 'Mười hai',
            13                  => 'Mười ba',
            14                  => 'Mười bốn',
            15                  => 'Mười năm',
            16                  => 'Mười sáu',
            17                  => 'Mười bảy',
            18                  => 'Mười tám',
            19                  => 'Mười chín',
            20                  => 'Hai mươi',
            30                  => 'Ba mươi',
            40                  => 'Bốn mươi',
            50                  => 'Năm mươi',
            60                  => 'Sáu mươi',
            70                  => 'Bảy mươi',
            80                  => 'Tám mươi',
            90                  => 'Chín mươi',
            100                 => 'trăm',
            1000                => 'ngàn',
            1000000             => 'triệu',
            1000000000          => 'tỷ',
            1000000000000       => 'nghìn tỷ',
            1000000000000000    => 'ngàn triệu triệu',
            1000000000000000000 => 'tỷ tỷ'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    public function datHang(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'nguoi_mua_hang' => 'required',
            'so_dien_thoai' => 'required',
            'dia_chi' => 'required'
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
        $data['ten'] = 'Đặt hàng online';
        if ($data['mua_hang']) {
            foreach ($data['danhSachHang'] as $item) {
                $tonKho = HangTonKho::where('san_pham_id', $item['id'])->first();
                if (!$tonKho || $tonKho->so_luong < $item['so_luong']) {
                    return response(['message' => 'Sản phẩm ' . $item['ten_san_pham'] . ' vượt quá số lượng tồn kho'], 500);
                }
            }
            if ($data['phuong_thuc_thanh_toan'] == 'tai_khoan') {
                $user = auth()->user();
                if (!$user) {
                    return response(['message' => 'Tài khoản không tồn tại'], 500);
                }
                $khacHang = KhachHang::where('user_id', $user->id)->first();
                if (!$khacHang) {
                    return response(['message' => 'Tài khoản khách hàng không tồn tại '], 500);
                }
                if ($data['tong_tien'] > $khacHang->so_du) {
                    return response(['message' => 'Số dư tài khoản không đủ'], 500);
                }
            }
        }
        try {
            DB::beginTransaction();
            $donHang = DonDatHang::create([
                'ma' => $data['ma'],
                'tong_tien' => $data['tong_tien'],
                'ten' => $data['ten'],
                'nguoi_mua_hang' => $data['nguoi_mua_hang'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'dia_chi' => $data['dia_chi'],
                'user_id' => $user ? $user->id : null,
                'ghi_chu' => $data['ghi_chu'],
                'trang_thai' => $data['mua_hang'] ? 'mua_hang_online' : 'dat_hang_online',
                'giam_gia' => $data['giam_gia'],
                // 'bang_gia_id' => $data['bang_gia_id'],
                // 'da_thanh_toan' => $data['da_thanh_toan'],        
                'con_phai_thanh_toan' => $data['tong_tien'],
                'thanh_toan' => $data['phuong_thuc_thanh_toan'],
                // 'phu_thu' => $data['trang_thai'] == 'hoa_don' ? $data['phu_thu'] : null,
                'thoi_gian_nhan_hang' => $data['thoi_gian_nhan_hang'],

            ]);
            if ($data['mua_hang']) {
                foreach ($data['danhSachHang'] as $item) {
                    SanPhamDonDatHang::create([
                        'san_pham_id' => $item['id'],
                        'gia_ban' => $item['gia_ban'],
                        'so_luong' => $item['so_luong'],
                        'don_dat_hang_id' => $donHang->id,
                        'doanh_thu' =>  $item['gia_ban'] * $item['so_luong']
                    ]);
                }
            } else {
                foreach ($data['datHang'] as $item) {
                    SanPhamDonDatHang::create([
                        'san_pham_id' => $item['id'],
                        'gia_ban' => $item['gia_ban'],
                        'so_luong' => $item['so_luong'],
                        'don_dat_hang_id' => $donHang->id,
                        'doanh_thu' =>  $item['gia_ban'] * $item['so_luong']
                    ]);
                }
            }
            DB::commit();
            return response(['message' => 'Thành công', 'don_hang_id' => $donHang->id], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể đặt hàng'], 500);
        }
    }

    public function khachHuyDon($id)
    {
        $user = auth()->user();
        $donHang = DonDatHang::where('id', $id)->first();
        if (!$donHang) {
            return response(['message' => 'Đơn hàng không tồn tại'], 501);
        }
        if (!$user || $user->id != $donHang->user_id) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            DB::beginTransaction();
            if ($donHang->trang_thai != 'moi_tao' && $donHang->trang_thai != 'mua_hang_online'  && $donHang->trang_thai != 'dat_hang_online') {
                return response(['message' => 'Không thể hủy đơn'], 500);
            }
            $donHang->update([
                'trang_thai' => 'khach_huy'
            ]);
            DB::commit();
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể hủy đơn'], 500);
        }
    }

    public function getPhieuThu(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $page = $request->get('page', 1);
        $query = PhieuThu::with('nguoiTao');
        $date = $request->get('date');
        $search = $request->get('search');
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if (isset($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('noi_dung', 'ilike', "%{$search}%")
                    ->orWhere('thong_tin_giao_dich', 'ilike', "%{$search}%")
                    ->orWhere('thong_tin_khach_hang', 'ilike', "%{$search}%");
                // ->orWhereHas('user', function ($query) use ($search) {
                //     $query->where('phone', 'ilike', "%{$search}%")
                //         ->orWhere('email', 'ilike', "%{$search}%")
                //         ->orWhere('name', 'ilike', "%{$search}%");
                // });
            });
        }
        $donHang = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $donHang,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function updatePhieuThu($id, Request $request)
    {
        $data = $request->all();
        try {
            PhieuThu::find($id)->update([
                'so_tien' => $data['so_tien'],
                'thong_tin_giao_dich' => $data['thong_tin_giao_dich'],
                'noi_dung' => $data['noi_dung'],
                'user_id_khach_hang' => $data['user_id_khach_hang'],
                'thong_tin_khach_hang' => $data['thong_tin_khach_hang'],
            ]);
            return response(['message' => 'Thanh cong'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật phiếu thu'], 500);
        }
    }

    public function addPhieuThu(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'so_tien' => 'required',
            'noi_dung' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm đơn vị'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }

        try {
            PhieuThu::create([
                'user_id_nguoi_tao' =>  auth()->user() ? auth()->user()->id : null,
                'type' => 'tu_nhap',
                'so_tien' => $data['so_tien'],
                'thong_tin_giao_dich' => $data['thong_tin_giao_dich'],
                'noi_dung' => $data['noi_dung'],
                'user_id_khach_hang' => $data['user_id_khach_hang'],
                'thong_tin_khach_hang' => $data['thong_tin_khach_hang'],
            ]);
            return response(['message' => 'Thanh cong'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể cập nhật phiếu thu'], 500);
        }
    }

    public function xoaPhieuThu($id)
    {
        try {
            PhieuThu::find($id)->delete();
            return response(['message' => 'Thanh cong'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa phiếu thu'], 500);
        }
    }

    public function inPhieuThu($id)
    {
        $donHang = PhieuThu::with('khachHang:id,name')->where('id', $id)->first();
        $time = Carbon::parse($donHang->created_at);
        $date = $time->day;
        $month = $time->month;
        $year = $time->year;
        $tien = $this->convert_number_to_words($donHang->so_tien);
        return view('phieuthu', ['ngay' => $date, 'thang' => $month, 'nam' => $year, 'data' => $donHang, 'tien' => $tien]);
    }

    public function getTonKhoDatTruoc($id)
    {
        $donHang = DonDatHang::whereIn('trang_thai', ['moi_tao', 'dat_hang_online'])->pluck('id')->toArray();
        $sanPhamDatTruoc = SanPhamDonDatHang::whereIn('don_dat_hang_id', $donHang)->where('san_pham_id', $id)->count('so_luong');
        $tonKho = HangTonKho::where('san_pham_id', $id)->first();
        if (!$tonKho) {
            $tonKho = 0;
        } else {
            $tonKho = $tonKho->so_luong;
        }
        return response(['dat_truoc' => $sanPhamDatTruoc, 'ton_kho' => $tonKho]);
    }

    public function getDonDoiTra(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $page = $request->get('page', 1);
        $query = DonDatHang::whereHas('traHang')->with('traHang', 'user', 'traHang.sanPham:id,ten_san_pham,don_vi_tinh');
        $date = $request->get('date');
        $search = $request->get('search');
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if (isset($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('ma', 'ilike', "%{$search}%")
                    ->orWhere('ten', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'ilike', "%{$search}%");
                        $query->orWhere('phone', 'ilike', "%{$search}%");
                        $query->orWhere('email', 'ilike', "%{$search}%");
                    });
            });
        }
        $donHang = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $donHang,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function thanhToanBoXung(Request $request)
    {
        try {
            $donHang = DonDatHang::where('id', $request->id)->first();
            $thanhToan = $request->thanh_toan + $donHang->da_thanh_toan;
            $phaiThanhToan = $donHang->con_phai_thanh_toan - $request->thanh_toan;
            $donHang->update(['da_thanh_toan' => $thanhToan, 'con_phai_thanh_toan' => $phaiThanhToan]);
            if (!$donHang->user_id && $request->hinh_thuc == 'tai_khoan') {
                return response(['message' => 'Khách hàng không tồn tại, không thể thanh toán qua tài khoản'], 500);
            }
            DB::beginTransaction();
            if ($request->hinh_thuc == 'tai_khoan') {
                $khach = KhachHang::where('user_id', $donHang->user_id)->first();
                if (!$khach) {
                    return response(['message' => 'Khách hàng không tồn tại, không thể thanh toán qua tài khoản'], 500);
                }
                if ($request->thanh_toan > $khach->so_du) {
                    return response(['message' => 'Số dư tài khoản không đủ'], 500);
                }
                $soDuMoi = $khach->so_du - $request->thanh_toan;
                $khach->update(['so_du' => $soDuMoi]);
                NopTien::create([
                    'trang_thai' => 'mua_hang',
                    'id_user_khach_hang' => $donHang->user_id,
                    'user_id' => auth()->user()->id,
                    'so_tien' => $request->thanh_toan,
                    'noi_dung' => 'Thanh toán bổ xung cho đơn hàng: ' . $donHang->ten . ' .Mã đơn: ' . $donHang->ma,
                    'so_du' => $soDuMoi,
                    'ma' => 'GD' . time()
                ]);
            }
            ThanhToanBoXung::create([
                'don_hang_id' => $request->id,
                'so_tien' => $request->thanh_toan,
                'hinh_thuc' => $request->hinh_thuc
            ]);
            $hinh_thuc_tt = $request->hinh_thuc == 'tien_mat' ? 'Tiền mặt' : ($request->hinh_thuc == 'chuyen_khoan' ? 'Chuyển khoản/Quẹt thẻ' : ($request->hinh_thuc == 'tai_khoan' ? 'Tài khoản' : ""));
            PhieuThu::create([
                'user_id_nguoi_tao' => auth()->user()->id,
                'type' => 'hoa_don',
                'reference_id' => $request->id,
                'so_tien' => $request->thanh_toan,
                'noi_dung' => "- Thanh toán bổ sung đơn hàng " . $donHang->ma . "\n" . "- Hình thức thanh toán: " . $hinh_thuc_tt . "\n" . "- Số tiền còn nợ của đơn hàng: " . $donHang->con_phai_thanh_toan . ' đ',
                'thong_tin_giao_dich' => null,
                'thong_tin_khach_hang' => null,
                'user_id_khach_hang' => $donHang->user_id ? $donHang->user_id : null
            ]);
            DB::commit();
            OneSignalFacade::sendNotificationUsingTags(
                "Bạn đã thanh toán số tiền: " . number_format($request->thanh_toan, 0, ",", ".") . 'đ. Bằng hình thức ' . $hinh_thuc_tt,
                array(["field" => "tag", "relation" => "=", "key" => "user_id", "value" => $donHang->user_id]),
                $url = null,
                $data = ['type' => 'task_new', 'id' =>  $donHang->user_id]
            );
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể thanh toán bổ sung'], 500);
        }
    }
    public function doiHang($id, Request $request)
    {
        $data = $request->all();
        try {
            if (count($data['danhSachHang']) > 0) {
                DB::beginTransaction();
                foreach ($data['danhSachHang'] as $item) {
                    if ($item['so_luong_doi_tra'] > 0) {
                        DoiTraHang::create([
                            'san_pham_id' => $item['hang_hoa']['id'],
                            'gia_ban' => $item['don_gia'],
                            'so_luong' => $item['so_luong_doi_tra'],
                            'don_hang_id' => $id,
                            'doanh_thu' => $item['don_gia'] * $item['so_luong_doi_tra'],
                            'type' => 'doi_hang',
                            'nguyen_nhan' => $item['nguyen_nhan_doi_hang']
                        ]);
                    }
                    $tonKho = HangTonKho::where('san_pham_id', $item['hang_hoa']['id'])->first();
                    if ($tonKho && ($tonKho->so_luong >= $item['so_luong_doi_tra'])) {
                        $tonKho->update(['so_luong' => $tonKho->so_luong - $item['so_luong_doi_tra']]);
                    } else {
                        return response(['message' => $item['hang_hoa']['ten_san_pham'] . ' không đủ tồn kho!'], 500);
                    }
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể đổi hàng'], 500);
        }
    }

    public function traHang($id, Request $request)
    {
        $data = $request->all();
        try {
            if (count($data['danhSachHang']) > 0) {
                DB::beginTransaction();
                SanPhamDonDatHang::where('don_dat_hang_id', $id)->delete();
                $soTien = 0;
                foreach ($data['danhSachHang'] as $item) {
                    if ($item['so_luong_doi_tra'] > 0) {
                        DoiTraHang::create([
                            'san_pham_id' => $item['hang_hoa']['id'],
                            'gia_ban' => $item['don_gia'],
                            'so_luong' => $item['so_luong_doi_tra'],
                            'don_hang_id' => $id,
                            'doanh_thu' => $item['don_gia'] * $item['so_luong_doi_tra'],
                            'type' => 'tra_hang',
                            'nguyen_nhan' => $item['nguyen_nhan_doi_hang']
                        ]);
                    }
                    SanPhamDonDatHang::create([
                        'san_pham_id' => $item['hang_hoa']['id'],
                        'gia_ban' => $item['don_gia'],
                        'so_luong' => $item['so_luong_ban_dau'] -  $item['so_luong_doi_tra'],
                        'don_dat_hang_id' => $id,
                        'doanh_thu' => $item['don_gia'] * ($item['so_luong_ban_dau'] -  $item['so_luong_doi_tra'])
                    ]);
                    $soTien = $soTien +  $item['don_gia'] * $item['so_luong_doi_tra'];
                }
                $donHang = DonDatHang::where('id', $id)->first();
                if ($donHang && $donHang->user_id && $data['phuong_thuc_hoan_tien'] == 'tai_khoan') {
                    $khacHang = KhachHang::where('user_id', $donHang->user_id)->first();
                    $khacHang->update(['so_du' =>  $khacHang->so_du + $soTien]);
                    NopTien::create([
                        'trang_thai' => 'hoan_tien',
                        'noi_dung' => 'Trả hàng, hoàn tiền. Đơn hàng: ' . $donHang->ten . ', mã đơn hàng: ' . $donHang->ma,
                        'id_user_khach_hang' => $khacHang->user_id,
                        'user_id' => auth()->user()->id,
                        'so_tien' => $soTien,
                        'so_du' => $khacHang->so_du,
                        'ma' => 'GD' . time()
                    ]);
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể đổi hàng'], 500);
        }
    }

    public function test()
    {
        try {
            $storage = Cache::get('tymon.jw'); // will return instance of FileStore
            $keys = [];
            dd($storage);

            return 'done';
        } catch (\Exception $e) {
            dd($e);
        }

        // return JWTAuth::getToken();
    }
}
