<?php

namespace App\Http\Controllers;

use App\HangTonKho;
use App\KiemKho;
use App\SanPham;
use App\SanPhamKiemKho;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function JmesPath\search;

class KiemKhoController extends Controller
{
    public function getSanPhamTonKho(Request $request)
    {
        $per_page = $request->get('per_page');
        $search = $request->get('search');
        $tonKho = HangTonKho::pluck('san_pham_id')->toArray();
        $sanPham = SanPham::query();
        $danhMuc = $request->get('danh_muc');
        if (isset($search)) {
            $search = trim($search);
            $sanPham =  $sanPham->where('ten_san_pham', 'ilike', "%{$search}%");
        }
        if (isset($danhMuc)) {
            $sanPham =  $sanPham->where('danh_muc_id', $danhMuc);
        }
        $sanPham = $sanPham->take($per_page)->get();
        foreach ($sanPham as $item) {
            if (in_array($item->id, $tonKho)) {
                $item['ton_kho'] = HangTonKho::where('san_pham_id', $item->id)->first()->so_luong;
            } else $item['ton_kho'] = 0;
        }
        return response(['data' => $sanPham], 200);
    }

    public function addKiemKe(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten' => 'required',
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

        try {
            DB::beginTransaction();
            $donHang = KiemKho::create([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'trang_thai' => 'moi_tao',
                'ghi_chu' => $data['ghi_chu'],
                'user_nhan_vien_id' => $data['user_nhan_vien_id'],
                'user_tao_id' => $user->id,
            ]);
            foreach ($data['danhSachHang'] as $item) {
                SanPhamKiemKho::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'kiem_kho_id' => $donHang->id,
                    'so_luong_truoc_kiem_kho' => $item['so_luong_truoc_kiem_kho']
                ]);
            }

            DB::commit();
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể tạo kiểm kho'], 500);
        }
    }

    public function getKiemKho(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = KiemKho::with('nhanVien', 'nguoiTao', 'sanPhams', 'sanPhams.sanPham:id,ten_san_pham,don_vi_tinh');
        $date = $request->get('date');
        $search = $request->get('search');
        $donHang = [];
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('ma', 'ilike', "%{$search}%")
                    ->orWhere('ten', 'ilike', "%{$search}%")
                    ->orWhereHas('nhanVien', function ($query) use ($search) {
                        $query->where('phone', 'ilike', "%{$search}%")
                            ->orWhere('email', 'ilike', "%{$search}%")
                            ->orWhere('name', 'ilike', "%{$search}%");
                    })
                    ->orWhereHas('nguoiTao', function ($query) use ($search) {
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

    public function getChiTietKiemKho($id)
    {
        $donHang = KiemKho::with('sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        return response(['data' => $donHang], 200);
    }

    public function kiemKho($id, Request $request)
    {
        $user = auth()->user();
        $data = $request->get('danhSachHang');
        $ghi_chu = $request->get('ghi_chu');
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            DB::beginTransaction();
            KiemKho::find($id)->update(['trang_thai' => 'da_kiem_kho', 'ghi_chu' => $ghi_chu]);
            if (count($data) > 0) {
                foreach ($data as $item) {
                    SanPhamKiemKho::where('kiem_kho_id', $id)->where('san_pham_id', $item['hang_hoa']['id'])->update([
                        'so_thuc_te' => $item['so_thuc_te'],
                        'so_chenh_lech' => $item['so_thuc_te'] - $item['ton_kho']
                    ]);
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không thể kiểm kho'], 500);
        }
    }

    public function duyetKiemKho($id, Request $request)
    {
        $user = auth()->user();
        $data = $request->get('danhSachHang');
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            DB::beginTransaction();
            KiemKho::find($id)->update(['trang_thai' => 'da_duyet']);
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $hangTonKho = HangTonKho::where('san_pham_id', $item['hang_hoa']['id'])->first();
                    if ($hangTonKho) {
                        $hangTonKho->update([
                            'so_luong' => $item['so_thuc_te']
                        ]);
                    } else {
                        HangTonKho::create([
                            'san_pham_id' => $item['hang_hoa']['id'],
                            'so_luong' => $item['so_thuc_te']
                        ]);
                    }
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['message' => 'Không duyệt kiểm kho'], 500);
        }
    }

    public function huyKiemKho($id)
    {
        $user = auth()->user();
        if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
            return response(['message' => 'Không có quyền'], 500);
        }
        try {
            KiemKho::find($id)->update(['trang_thai' => 'da_huy']);
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể hủy kiểm kho'], 500);
        }
    }

    public function xoaKiemKho($id)
    {
        try {
            $user = auth()->user();
            if (!$user || ($user->role_id != 1 && $user->role_id != 2)) {
                return response(['message' => 'Không có quyền'], 500);
            }
            $kiemKho = KiemKho::where('id', $id)->first();
            if ($kiemKho->trang_thai != 'moi_tao' && $kiemKho->trang_thai != 'da_huy') {
                return response(['message' => 'Không thể xóa phiếu kiểm kho này'], 500);
            }
            $kiemKho->delete();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa phiếu kiểm kho này'], 500);
        }
    }

    public function getNhanVien(){
        $nhanVien = User::where('role_id', 2)->get();
        return $nhanVien;
    }
}
