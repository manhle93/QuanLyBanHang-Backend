<?php

namespace App\Http\Controllers;

use App\DonViThucTapChuaChay;
use App\NhanSuThucTapPhuongAnChuaChay;
use App\PhuongTienThucTapPhuongAnChuaChay;
use App\QuanHuyenThucTapPhuongAnChuaChay;
use App\ThucTapPhuongAnChuaChay;
use Carbon\Carbon;
use DB;


use Illuminate\Http\Request;

class ThucTapPhuongAnChuaChayController extends Controller
{


    public function create(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            if (isset($data['ngay_lap_phuong_an'])) {
                $data['ngay_lap_phuong_an'] = Carbon::parse($data['ngay_lap_phuong_an'])->timezone('Asia/Ho_Chi_Minh');
            }
            $phuongAn = ThucTapPhuongAnChuaChay::create([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'thoi_gian' => Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh'),
                'ngay_lap_phuong_an' => $data['ngay_lap_phuong_an'],
                'tinh_huong' => $data['tinh_huong'],
                'danh_gia' => $data['danh_gia'],
                'toa_nha_id' => $data['toa_nha_id'],
                'cap_phe_duyet' => $data['cap_phe_duyet']
            ]);
            foreach ($data['don_vi_tham_gia'] as $items) {
                DonViThucTapChuaChay::create([
                    'don_vi_pccc_id' => $items['don_vi_tham_gia'],
                    'phuong_an_thuc_tap_id' => $phuongAn->id
                ]);
                if (count($items['phuong_tien_tham_gia']) > 0) {
                    for ($i = 0; $i < count($items['phuong_tien_tham_gia']); $i++) {
                        PhuongTienThucTapPhuongAnChuaChay::create([
                            'don_vi_pccc_tham_gia_id' => $items['don_vi_tham_gia'],
                            'quan_huyen_tham_gia_id' => null,
                            'phuong_an_thuc_tap_id' => $phuongAn->id,
                            'phuong_tien_pccc_id' => $items['phuong_tien_tham_gia'][$i]
                        ]);
                    }
                }
                if (count($items['nhan_su_tham_gia']) > 0) {
                    for ($j = 0; $j < count($items['nhan_su_tham_gia']); $j++) {
                        NhanSuThucTapPhuongAnChuaChay::create([
                            'don_vi_pccc_tham_gia_id' => $items['don_vi_tham_gia'],
                            'quan_huyen_tham_gia_id' => null,
                            'phuong_an_thuc_tap_id' => $phuongAn->id,
                            'can_bo_chien_si_id' => $items['nhan_su_tham_gia'][$j]
                        ]);
                    }
                }
            }
            foreach ($data['quan_huyen_tham_gia'] as $els) {
                QuanHuyenThucTapPhuongAnChuaChay::create([
                    'quan_huyen_id' => $els['quan_huyen_tham_gia'],
                    'phuong_an_thuc_tap_id' => $phuongAn->id
                ]);
                if (count($els['phuong_tien_tham_gia']) > 0) {
                    for ($i = 0; $i < count($els['phuong_tien_tham_gia']); $i++) {
                        PhuongTienThucTapPhuongAnChuaChay::create([
                            'don_vi_pccc_tham_gia_id' => null,
                            'quan_huyen_tham_gia_id' => $els['quan_huyen_tham_gia'],
                            'phuong_an_thuc_tap_id' => $phuongAn->id,
                            'phuong_tien_pccc_id' => $els['phuong_tien_tham_gia'][$i]
                        ]);
                    }
                }
                if (count($els['nhan_su_tham_gia']) > 0) {
                    for ($j = 0; $j < count($els['nhan_su_tham_gia']); $j++) {
                        NhanSuThucTapPhuongAnChuaChay::create([
                            'don_vi_pccc_tham_gia_id' => null,
                            'quan_huyen_tham_gia_id' => $els['quan_huyen_tham_gia'],
                            'phuong_an_thuc_tap_id' => $phuongAn->id,
                            'can_bo_chien_si_id' => $els['nhan_su_tham_gia'][$j]
                        ]);
                    }
                }
            }
            DB::commit();
            return response(['message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['data' => $e, 'message' => 'Không thể thêm mới'], 500);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $date = $request->get('ngay_kiem_tra');
        // $query = ThucTapPhuongAnChuaChay::query()->with('donVi', 'quanHuyen', 'quanHuyen.phuongTien', 'quanHuyen.canBoChienSi', 'donVi.phuongTien', 'donVi.canBoChienSi', 'donVi.donVi:id,ten', 'quanHuyen.quanHuyen:id,name', 'quanHuyen.phuongTien.phuongTien:id,ten,loai_phuong_tien_pccc_id', 'donVi.phuongTien.phuongTien:id,ten,loai_phuong_tien_pccc_id', 'quanHuyen.phuongTien.phuongTien.loaiPhuongTienPccc:id,ten', 'donVi.phuongTien.phuongTien.loaiPhuongTienPccc:id,ten');
        // $query = ThucTapPhuongAnChuaChay::query()->with('donVi', 'quanHuyen', 'quanHuyen.phuongTien', 'quanHuyen.canBoChienSi', 'donVi.phuongTien', 'donVi.canBoChienSi', 'donVi.donVi:id,ten', 'quanHuyen.quanHuyen:id,name');
        $query = ThucTapPhuongAnChuaChay::query()->with('donVi', 'quanHuyen');
        if (isset($date)) {
            $query->where('ngay_lap_phuong_an', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('ngay_lap_phuong_an', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if (!empty($tinh_thanh_id)) {
            $query->where('tinh_thanh_id', $tinh_thanh_id);
        }
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query
                    ->where('tinh_huong', 'ilike', "%{$search}%")
                    ->orWhere('danh_gia', 'ilike', "%{$search}%");
            });
        }
        $query->orderBy('updated_at', 'desc');
        $tinhs = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $tinhs,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            ThucTapPhuongAnChuaChay::find($id)->delete();
            DonViThucTapChuaChay::where('phuong_an_thuc_tap_id', $id)->delete();
            QuanHuyenThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $id)->delete();
            PhuongTienThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $id)->delete();
            NhanSuThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $id)->delete();
            DB::commit();
            return response(['message' => 'Xoá thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['data' => $e, 'message' => 'Không thể xóa'], 500);
        }
    }

    public function show($id)
    {
        $query = ThucTapPhuongAnChuaChay::query()->where('id', $id)->with('donVi', 'quanHuyen')->first();
        $nhanSu = NhanSuThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $id)->with('donViPccc', 'quanHuyen', 'canBoChienSi')->get();
        $phuongTien = PhuongTienThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $id)->with('phuongTien', 'donViPccc', 'quanHuyen')->get();
        return response(['data' => $query, 'nhan_su' => $nhanSu, 'phuong_tien' => $phuongTien], 200);
    }

    public function update(Request $request, $id)
    {

        $data = $request->all();
        try {
            DB::beginTransaction();
            if (isset($data['ngay_lap_phuong_an'])) {
                $data['ngay_lap_phuong_an'] = Carbon::parse($data['ngay_lap_phuong_an'])->timezone('Asia/Ho_Chi_Minh');
            }
            $phuongAn = ThucTapPhuongAnChuaChay::where('id', $id)->first()->update([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'thoi_gian' => Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh'),
                'tinh_huong' => $data['tinh_huong'],
                'toa_nha_id' => $data['toa_nha_id'],
                'cap_phe_duyet' => $data['cap_phe_duyet'],
                'ngay_lap_phuong_an' => $data['ngay_lap_phuong_an'],
                'danh_gia' => $data['danh_gia'],
            ]);
            DonViThucTapChuaChay::where('phuong_an_thuc_tap_id', $id)->delete();
            QuanHuyenThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $id)->delete();
            PhuongTienThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $id)->delete();
            NhanSuThucTapPhuongAnChuaChay::where('phuong_an_thuc_tap_id', $id)->delete();
            foreach ($data['don_vi_tham_gia'] as $items) {
                DonViThucTapChuaChay::create([
                    'don_vi_pccc_id' => $items['don_vi_tham_gia'],
                    'phuong_an_thuc_tap_id' => $id
                ]);
                foreach ($items['phuong_tien_tham_gia'] as $pt) {
                    PhuongTienThucTapPhuongAnChuaChay::create([
                        'don_vi_pccc_tham_gia_id' => $items['don_vi_tham_gia'],
                        'quan_huyen_tham_gia_id' => null,
                        'phuong_an_thuc_tap_id' => $id,
                        'phuong_tien_pccc_id' => $pt['phuong_tien_pccc_id']
                    ]);
                }

                foreach ($items['nhan_su_tham_gia'] as $ns) {
                    NhanSuThucTapPhuongAnChuaChay::create([
                        'don_vi_pccc_tham_gia_id' => $items['don_vi_tham_gia'],
                        'quan_huyen_tham_gia_id' => null,
                        'phuong_an_thuc_tap_id' => $id,
                        'can_bo_chien_si_id' => $ns['can_bo_chien_si_id']
                    ]);
                }
            }
            foreach ($data['quan_huyen_tham_gia'] as $els) {
                QuanHuyenThucTapPhuongAnChuaChay::create([
                    'quan_huyen_id' => $els['quan_huyen_tham_gia'],
                    'phuong_an_thuc_tap_id' => $id
                ]);
                foreach ($els['phuong_tien_tham_gia'] as $pt) {
                    PhuongTienThucTapPhuongAnChuaChay::create([
                        'don_vi_pccc_tham_gia_id' => null,
                        'quan_huyen_tham_gia_id' => $els['quan_huyen_tham_gia'],
                        'phuong_an_thuc_tap_id' => $id,
                        'phuong_tien_pccc_id' => $pt['phuong_tien_pccc_id']
                    ]);
                }

                foreach ($els['nhan_su_tham_gia'] as $ns) {
                    NhanSuThucTapPhuongAnChuaChay::create([
                        'don_vi_pccc_tham_gia_id' => null,
                        'quan_huyen_tham_gia_id' => $els['quan_huyen_tham_gia'],
                        'phuong_an_thuc_tap_id' => $id,
                        'can_bo_chien_si_id' => $ns['can_bo_chien_si_id']
                    ]);
                }
            }
            DB::commit();
            return response(['message' => 'Cập nhật thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['data' => $e, 'message' => 'Không thể cập nhật'], 500);
        }
    }
}
