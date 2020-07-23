<?php

namespace App\Http\Controllers;

use App\HangTonKho;
use App\SanPhamXuatHuy;
use App\XuatHuy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class XuatHuyController extends Controller
{
    public function addXuaHuy(Request $request)
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
            $donHang = XuatHuy::create([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'ghi_chu' => $data['ghi_chu'],
                'user_tao_id' => $user->id,
            ]);
            foreach ($data['danhSachHang'] as $item) {
                SanPhamXuatHuy::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'xuat_huy_id' => $donHang->id,
                    'ton_kho_truoc_xuat_huy' => $item['ton_kho_truoc_xuat_huy'],
                    'ton_kho_sau_xuat_huy' => $item['ton_kho_truoc_xuat_huy'] - $item['so_xuat_huy'],
                    'so_xuat_huy' => $item['so_xuat_huy'],
                ]);

                $hangTonKho = HangTonKho::where('san_pham_id', $item['hang_hoa']['id'])->first();
                if ($hangTonKho) {
                    $hangTonKho->update([
                        'so_luong' => $item['ton_kho_truoc_xuat_huy'] - $item['so_xuat_huy']
                    ]);
                } else {
                    HangTonKho::create([
                        'san_pham_id' => $item['hang_hoa']['id'],
                        'so_luong' =>  $item['ton_kho_truoc_xuat_huy'] - $item['so_xuat_huy']
                    ]);
                }
            }


            DB::commit();
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể xuất hủy'], 500);
        }
    }

    public function getXuatHuy(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = XuatHuy::with('nguoiTao', 'sanPhams', 'sanPhams.sanPham:id,ten_san_pham,don_vi_tinh');
        $date = $request->get('date');
        $search = $request->get('search');
        $donHang = [];
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if(isset($search)){
                $query->where('ten', 'ilike', "%{$search}%");
                $query->orWhere('ma', 'ilike', "%{$search}%");
            
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
    public function getChiTietXuatHuy($id)
    {
        $donHang = XuatHuy::with('sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        return response(['data' => $donHang], 200);
    }
}
