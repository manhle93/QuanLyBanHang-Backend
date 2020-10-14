<?php

namespace App\Http\Controllers;

use App\DonHangNhaCungCap;
use App\DonHangThanhToanNhaCungCap;
use App\HangTonKho;
use App\Kho;
use App\NhaCungCap;
use App\PhieuNhapKho;
use App\SanPham;
use App\SanPhamDonHangNhaCungCap;
use App\SanPhamTraNhaCungCap;
use App\ThanhToanNhaCungCap;
use App\TraHangNhaCungCap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Exception;

class DonHangNhaCungCapController extends Controller
{
    public function getDonHang(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $date = $request->get('date');
        $trang_thai = $request->get('trang_thai');
        $nhac_cung_cap = $request->get('nha_cung_cap');
        $query = DonHangNhaCungCap::with('user', 'sanPhams');
        $donHang = [];
        if (isset($nhac_cung_cap)) {
            $query = $query->where('user_id', $nhac_cung_cap);
        }
        if (isset($trang_thai)) {
            $query = $query->whereIn('trang_thai', $trang_thai);
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
        return response()->json([
            'data' => $donHang,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function addDonHang(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'thoi_gian' => 'required',
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
        $user = auth()->user();
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập'], 500);
        }
        if ($user->role_id == 3) {
            $data['nha_cung_cap_id'] = null;
        }
        if ($user->role_id != 3 && $user->role_id != 2 && $user->role_id != 1) {
            return response(['message' => 'Không có quyền'], 4001);
        }
        try {
            DB::beginTransaction();
            $data['thoi_gian'] = Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
            $donHang = DonHangNhaCungCap::create([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'ghi_chu' => $data['ghi_chu'],
                'chiet_khau' => $data['chiet_khau'],
                'tong_tien' => $data['tong_tien'],
                'thoi_gian' => $data['thoi_gian'],
                'user_id' => $data['nha_cung_cap_id'] ? $data['nha_cung_cap_id'] : $user->id,
                'trang_thai' => 'moi_tao'
            ]);
            foreach ($data['danhSachHang'] as $item) {
                SanPhamDonHangNhaCungCap::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'don_gia' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_hang_id' => $donHang->id
                ]);
            }
            DB::commit();
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể thêm mới'], 500);
        }
    }
    public function getChiTietDonHang($id)
    {
        $donHang = DonHangNhaCungCap::with('sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        return response(['data' => $donHang], 200);
    }
    public function update($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'thoi_gian' => 'required',
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
        $user = auth()->user();
        if (!$user) {
            return response(['message' => 'Chưa đăng nhập'], 500);
        }
        if ($user->role_id == 3) {
            $data['nha_cung_cap_id'] = null;
        }
        if ($user->role_id != 3 && $user->role_id != 2 && $user->role_id != 1) {
            return response(['message' => 'Không có quyền'], 4001);
        }
        try {
            DB::beginTransaction();
            $data['thoi_gian'] = Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
            $donHang = DonHangNhaCungCap::where('id', $id)->first()->update([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'ghi_chu' => $data['ghi_chu'],
                'chiet_khau' => $data['chiet_khau'],
                'tong_tien' => $data['tong_tien'],
                'thoi_gian' => $data['thoi_gian'],
                'user_id' => $data['nha_cung_cap_id'] ? $data['nha_cung_cap_id'] : $user->id,
                'trang_thai' => 'moi_tao'
            ]);
            SanPhamDonHangNhaCungCap::where('don_hang_id', $id)->delete();
            foreach ($data['danhSachHang'] as $item) {
                SanPhamDonHangNhaCungCap::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'don_gia' => $item['don_gia'],
                    'so_luong' => $item['so_luong'],
                    'don_hang_id' => $id,
                    'so_luong_thuc_te' => $item['so_luong_thuc_te']
                ]);
            }
            DB::commit();
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể cập nhật'], 500);
        }
    }

    public function duyetDon($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }
        $donHang = DonHangNhaCungCap::where('id', $id)->first();
        if ($user->role_id == 1 || $user->id == $donHang->user_id) {
            try {
                DonHangNhaCungCap::find($id)->update(['trang_thai' => 'da_duyet']);
                return response(['message' => "Duyệt đơn thành công"], 200);
            } catch (\Exception $e) {
                return response(['message' => "Không thể duyệt đơn"], 500);
            }
        } else  return response(['message' => "Không có quyền duyệt đơn"], 401);
    }

    public function huyDon($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }
        $donHang = DonHangNhaCungCap::where('id', $id)->first();
        if ($user->role_id == 1 || $user->id == $donHang->user_id) {
            try {
                DonHangNhaCungCap::find($id)->update(['trang_thai' => 'huy_bo']);
                return response(['message' => "Hủy đơn thành công"], 200);
            } catch (\Exception $e) {
                return response(['message' => "Không thể duyệt đơn"], 500);
            }
        } else  return response(['message' => "Không có quyền hủy đơn"], 401);
    }

    public function xoaDon($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }
        $donHang = DonHangNhaCungCap::where('id', $id)->first();

        if ($user->role_id == 1 || $donHang->user_id == $user->id) {
            try {
                if ($donHang->trang_thai == 'nhap_kho') {
                    return response(['message' => "Không thể xóa đơn hàng đã nhập kho"], 500);
                }
                $donHang->delete();
                return response(['message' => "Xóa đơn thành công"], 200);
            } catch (\Exception $e) {
                return response(['message' => "Không thể xóa đơn hàng"], 500);
            }
        } else return response(['message' => "Không có quyền xóa đơn"], 401);
    }

    public function nhapKho($id, Request $request)
    {
        $user = auth()->user();
        $kho_id = $request->kho_id;
        $so_tien = $request->so_tien_thanh_toan;
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }
        if (!$kho_id) {
            return response(['message' => "Chưa chọn kho"], 400);
        }
        if ($user->role_id != 1 && $user->role_id != 2) {
            return response(['message' => "Không có quyền nhập kho"], 402);
        }
        try {
            DB::beginTransaction();
            DonHangNhaCungCap::where('id', $id)->first()->update(['trang_thai' => 'nhap_kho', 'da_thanh_toan' => $so_tien]);
            PhieuNhapKho::create(['don_hang_id' => $id, 'ma' => 'PNK' . $id, 'user_id' => $user->id, 'kho_id' => $kho_id]);
            $hangHoa = SanPhamDonHangNhaCungCap::where('don_hang_id', $id)->get();
            foreach ($hangHoa as $item) {
                $checkKho = HangTonKho::where('san_pham_id', $item->san_pham_id)->where('kho_id', $kho_id)->first();
                if ($checkKho) {
                    $checkKho->update(['so_luong' => $checkKho->so_luong + $item->so_luong_thuc_te]);
                } else {
                    HangTonKho::create(['san_pham_id' => $item->san_pham_id, 'so_luong' => $item->so_luong_thuc_te, 'kho_id' => $kho_id]);
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể tạo phiếu nhập'], 500);
        }
    }
    public function traHangNhaCungCap(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nha_cung_cap_id' => 'required',
            'hangHoas' => 'required',
            'tong_tien' => 'required',
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
        DB::beginTransaction();
        try {
            $don = TraHangNhaCungCap::create([
                'ma_don' => 'THNCC' . time(),
                'nha_cung_cap_id' => $data['nha_cung_cap_id'],
                'tong_tien' => $data['tong_tien'],
                'ly_do' => $data['ly_do']
            ]);
            foreach ($data['hangHoas'] as $item) {
                SanPhamTraNhaCungCap::create([
                    'san_pham_id' => $item['san_pham_id'],
                    'don_tra_hang_id' => $don->id,
                    'so_luong' => $item['so_luong'],
                    'don_gia' => $item['don_gia'],
                    'thanh_tien' => $item['so_luong'] * $item['don_gia']
                ]);
                $tonKho = HangTonKho::where('san_pham_id', $item['san_pham_id'])->first();
                $tenSP = SanPham::where('id', $item['san_pham_id'])->first()->ten_san_pham;
                if (!$tonKho || $tonKho->so_luong == 0) {
                    return response(['message' => 'Sản phẩm ' . $tenSP . ' đã hết hàng trong kho'], 500);
                }
                $soLuongMoi = $tonKho->so_luong - $item['so_luong'];
                if ($soLuongMoi < 0) {
                    return response(['message' => 'Sản phẩm ' . $tenSP . 'trả quá số lượng tồn kho'], 500);
                }
                $tonKho->update(['so_luong' => $soLuongMoi]);
            }
            DB::commit();

            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể thêm mới'], 500);
        }
    }
    public function getDonTraHang(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $nhac_cung_cap = $request->get('nha_cung_cap');
        $date = $request->get('date');
        $query = TraHangNhaCungCap::with('sanPhams', 'nhaCungCap:id,ten');
        $user = auth()->user();
        if (!$user) {
            return [];
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if (isset($nhac_cung_cap)) {
            $query->where('nha_cung_cap_id', $nhac_cung_cap);
        }
        $nCC = NhaCungCap::where('user_id', $user->id)->first();
        if ($user->role_id == 3 && $nCC) {
            $query->where('nha_cung_cap_id', $nCC->id);
        }
        $query->orderBy('created_at', 'desc');
        $query->orderBy('id', 'asc');
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }
    public function xoaDonTrahang($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }

        if ($user->role_id != 1 && $user->role_id != 2) {
            return response(['message' => "Không có quyền"], 402);
        }
        try {
            DB::beginTransaction();
            $sanPham = SanPhamTraNhaCungCap::where('don_tra_hang_id', $id)->get();
            foreach ($sanPham as $item) {
                $tonKho =  HangTonKho::where('san_pham_id', $item['san_pham_id'])->first();
                $soLuongMoi = $tonKho->so_luong + $item['so_luong'];
                $tonKho->update(['so_luong' => $soLuongMoi]);
            }
            TraHangNhaCungCap::find($id)->delete();
            DB::commit();
            return response(['message' => "Thành công"], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => "Không thể xóa"], 500);
        }
    }
    public function updateDonTraHang($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nha_cung_cap_id' => 'required',
            'hangHoas' => 'required',
            'tong_tien' => 'required',
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
        DB::beginTransaction();
        try {
            TraHangNhaCungCap::find($id)->update([
                'ma_don' => 'THNCC' . time(),
                'nha_cung_cap_id' => $data['nha_cung_cap_id'],
                'tong_tien' => $data['tong_tien'],
                'ly_do' => $data['ly_do']
            ]);
            foreach ($data['donHangCu'] as $item) {
                $tonKho =  HangTonKho::where('san_pham_id', $item['san_pham_id'])->first();
                $soLuongMoi = $tonKho->so_luong + $item['so_luong'];
                $tonKho->update(['so_luong' => $soLuongMoi]);
            }
            SanPhamTraNhaCungCap::where('don_tra_hang_id', $id)->delete();
            foreach ($data['hangHoas'] as $item) {
                SanPhamTraNhaCungCap::create([
                    'san_pham_id' => $item['san_pham_id'],
                    'don_tra_hang_id' => $id,
                    'so_luong' => $item['so_luong'],
                    'don_gia' => $item['don_gia'],
                    'thanh_tien' => $item['so_luong'] * $item['don_gia']
                ]);
                $tonKho = HangTonKho::where('san_pham_id', $item['san_pham_id'])->first();
                $tenSP = SanPham::where('id', $item['san_pham_id'])->first()->ten_san_pham;
                if (!$tonKho || $tonKho->so_luong == 0) {
                    return response(['message' => 'Sản phẩm ' . $tenSP . ' đã hết hàng trong kho'], 500);
                }
                $soLuongMoi = $tonKho->so_luong - $item['so_luong'];
                if ($soLuongMoi < 0) {
                    return response(['message' => 'Sản phẩm ' . $tenSP . 'trả quá số lượng tồn kho'], 500);
                }
                $tonKho->update(['so_luong' => $soLuongMoi]);
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể cập nhật'], 500);
        }
    }
    public function inHoaDon($id)
    {
        $donHang = DonHangNhaCungCap::with('sanPhams', 'sanPhams.sanPham:id,ten_san_pham,don_vi_tinh', 'user:id,name')->where('id', $id)->first();
        $time = Carbon::parse($donHang->updated_at);
        $date = $time->day;
        $month = $time->month;
        $year = $time->year;
        $tien = $this->convert_number_to_words($donHang->tong_tien);
        return view('hoadonnhacc', ['ngay' => $date, 'thang' => $month, 'nam' => $year, 'data' => $donHang, 'tien' => $tien]);
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

    public function getDonHangNhaCCC($id)
    {
        $ncc = NhaCungCap::where('user_id', $id)->first();
        if (!$ncc) {
            return [];
        }
        $trahang = TraHangNhaCungCap::where('nha_cung_cap_id', $ncc->id)->get();
        $donHang = DonHangNhaCungCap::where('user_id', $id)->where('trang_thai', 'nhap_kho')->get();
        return ['tra_hang' => $trahang, 'don_hang' => $donHang];
    }

    public function addDonThanhToanNCC(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nha_cung_cap_id' => 'required',
            'hangHoas' => 'required',
            'phai_thanh_toan' => 'required',
            'thanh_toan' => 'required',
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
            DB::beginTransaction();
            $don = ThanhToanNhaCungCap::create([
                'ma_don' => 'TTNCC' . time(),
                'phai_thanh_toan' => $data['phai_thanh_toan'],
                'thanh_toan' => $data['thanh_toan'],
                'user_id' => auth()->user()->id,
                'nha_cung_cap_id' => $data['nha_cung_cap_id']
            ]);
            foreach ($data['hangHoas'] as $item) {
                DonHangThanhToanNhaCungCap::create([
                    'don_thanh_toan_id' => $don->id,
                    'don_hang_id' =>  $item['loai'] == 'mua_hang' ? $item['id'] : null,
                    'don_tra_hang_id' => $item['loai'] == 'tra_hang' ? $item['id'] : null,
                    'loai' => $item['loai']
                ]);
                if ($item['loai'] == 'mua_hang') {
                    DonHangNhaCungCap::find($item['id'])->update(['thanh_toan' => true]);
                }
                if ($item['loai'] == 'tra_hang') {
                    TraHangNhaCungCap::find($item['id'])->update(['thanh_toan' => true]);
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể thêm mới'], 500);
        }
    }
    public function getLichSuThanhToanNCC(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $nhac_cung_cap = $request->get('nha_cung_cap');
        $date = $request->get('date');
        $query = ThanhToanNhaCungCap::with('donHangs', 'nhanCungCap:id,ten', 'user:id,name');
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if ($nhac_cung_cap) {
            $query->where('nha_cung_cap_id', $nhac_cung_cap);
        }
        $query->orderBy('created_at', 'desc');
        $query->orderBy('id', 'asc');
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }
    public function updateDonThanhToanNCC($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nha_cung_cap_id' => 'required',
            'hangHoas' => 'required',
            'phai_thanh_toan' => 'required',
            'thanh_toan' => 'required',
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
            DB::beginTransaction();
            $don = ThanhToanNhaCungCap::find($id)->update([
                'ma_don' => 'TTNCC' . time(),
                'phai_thanh_toan' => $data['phai_thanh_toan'],
                'thanh_toan' => $data['thanh_toan'],
                'user_id' => auth()->user()->id,
                'nha_cung_cap_id' => $data['nha_cung_cap_id']
            ]);
            DonHangThanhToanNhaCungCap::where('don_thanh_toan_id', $id)->delete();
            foreach ($data['donHangCus'] as $item) {
                if ($item['loai'] == 'mua_hang') {
                    DonHangNhaCungCap::find($item['id'])->update(['thanh_toan' => false]);
                }
                if ($item['loai'] == 'tra_hang') {
                    TraHangNhaCungCap::find($item['id'])->update(['thanh_toan' => false]);
                }
            }
            foreach ($data['hangHoas'] as $item) {
                DonHangThanhToanNhaCungCap::create([
                    'don_thanh_toan_id' => $id,
                    'don_hang_id' =>  $item['loai'] == 'mua_hang' ? $item['id'] : null,
                    'don_tra_hang_id' => $item['loai'] == 'tra_hang' ? $item['id'] : null,
                    'loai' => $item['loai']
                ]);
                if ($item['loai'] == 'mua_hang') {
                    DonHangNhaCungCap::find($item['id'])->update(['thanh_toan' => true]);
                }
                if ($item['loai'] == 'tra_hang') {
                    TraHangNhaCungCap::find($item['id'])->update(['thanh_toan' => true]);
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return response(['message' => 'Không thể cập nhật'], 500);
        }
    }
    public function xoaDonThanhToanNCC($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['message' => "Chưa đăng nhập"], 400);
        }

        if ($user->role_id != 1 && $user->role_id != 2) {
            return response(['message' => "Không có quyền"], 402);
        }
        try {
            ThanhToanNhaCungCap::find($id)->delete();
            return response(['message' => "Thành công"], 200);
        } catch (\Exception $e) {
            return response(['message' => "Không thể xóa"], 500);
        }
    }
}
