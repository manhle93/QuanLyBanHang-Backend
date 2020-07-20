<?php

namespace App\Http\Controllers;

use App\HangTonKho;
use App\KiemKho;
use App\SanPham;
use App\SanPhamKiemKho;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KiemKhoController extends Controller
{
    public function getSanPhamTonKho(Request $request){
        $per_page = $request->get('per_page', 6);
        $search = $request->get('search');
        $tonKho = HangTonKho::pluck('san_pham_id')->toArray();
        $sanPham = SanPham::query();
        if(isset($search)){
            $search = trim($search);
            $sanPham =  $sanPham->where('ten_san_pham', 'ilike', "%{$search}%");
        }
        $sanPham = $sanPham->take($per_page)->get();
        foreach($sanPham as $item){
            if(in_array($item->id, $tonKho)){
                $item['ton_kho'] = HangTonKho::where('san_pham_id', $item->id)->first()->so_luong;
            }else $item['ton_kho'] = 0;
        }
        return response(['data'=>$sanPham],200);
    }

    public function addKiemKe(Request $request){
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
                'user_tao_id' => $user->id
            ]);
            foreach ($data['danhSachHang'] as $item) {
                SanPhamKiemKho::create([
                    'san_pham_id' => $item['hang_hoa']['id'],
                    'kiem_kho_id' => $donHang->id,
                ]);
            }

            DB::commit();
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => 'Không thể tạo kiểm kho'], 500);
        }
    }

    public function getKiemKho(Request $request){
        $user = auth()->user();
        $perPage = $request->query('per_page', 5);
        $page = $request->get('page', 1);
        $query = KiemKho::with('nhanVien','nguoiTao', 'sanPhams', 'sanPhams.sanPham:id,ten_san_pham,don_vi_tinh');
        $date = $request->get('date');
        $donHang = [];
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

    public function getChiTietKiemKho($id){
        $tonKho = HangTonKho::pluck('san_pham_id')->toArray();
        $donHang = KiemKho::with('sanPhams', 'sanPhams.sanPham')->where('id', $id)->first();
        foreach($donHang->sanPhams as $item){
            if(in_array($item->id, $tonKho)){
                $item['ton_kho'] = HangTonKho::where('san_pham_id', $item->id)->first()->so_luong;
            }else $item['ton_kho'] = 0;
        }

        return response(['data' => $donHang], 200);
    }
}
