<?php

namespace App\Http\Controllers;

use App\LichTruc;
use App\NhanSuPhuongTienTruc;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use DB;
use PhpParser\Node\Stmt\TryCatch;
use Validator;


class LichTrucController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'tinh_thanh_id' => 'required',
            'don_vi_id' => 'required',
            'ngay_truc' => 'required',
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
        $data['ngay_truc'] = Carbon::parse($data['ngay_truc']);
        DB::beginTransaction();
        try {
            $lichTruc = LichTruc::create([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'don_vi_id' => $data['don_vi_id'],
                'ngay_truc' => $data['ngay_truc'],
                'chi_huy_doi' => $data['chi_huy_doi'],
                'can_bo_tong_hop' => $data['can_bo_tong_hop'],
                'tong_cbcs' => $data['tong_cbcs'],
                'co_mat' => $data['co_mat'],
                'hanh_chinh' => $data['hanh_chinh'],
                'vang_mat' => $data['vang_mat'],
                'tinh_hinh_trong_ngay' => $data['tinh_hinh_trong_ngay'],
            ]);
            if (count($data['phuong_tien_pccc_id']) > 0) {
                foreach ($data['phuong_tien_pccc_id'] as $pt) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $lichTruc->id,
                        'type' => 'phuong_tien',
                        'reference_id' =>  $pt,
                        'chi_tiet_nhan_su' => null
                    ]);
                }
            }
            if (count($data['truc_ban']) > 0) {
                foreach ($data['truc_ban'] as $tb) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $lichTruc->id,
                        'type' => 'cbcs',
                        'reference_id' =>  $tb,
                        'chi_tiet_nhan_su' => 'truc_ban'
                    ]);
                }
            }
            if (count($data['truc_chi_huy']) > 0) {
                foreach ($data['truc_chi_huy'] as $tth) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $lichTruc->id,
                        'type' => 'cbcs',
                        'reference_id' =>  $tth,
                        'chi_tiet_nhan_su' => 'truc_chi_huy'
                    ]);
                }
            }
            if (count($data['truc_kiem_tra']) > 0) {
                foreach ($data['truc_kiem_tra'] as $tkt) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $lichTruc->id,
                        'type' => 'cbcs',
                        'reference_id' =>  $tkt,
                        'chi_tiet_nhan_su' => 'truc_kiem_tra'
                    ]);
                }
            }
            if (count($data['truc_thong_tin']) > 0) {
                foreach ($data['truc_thong_tin'] as $ttt) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $lichTruc->id,
                        'type' => 'cbcs',
                        'reference_id' =>  $ttt,
                        'chi_tiet_nhan_su' => 'truc_thong_tin'
                    ]);
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'data' => $e,
                'message' => 'Không thể thêm mới',
                'code' => 500,
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $don_vi = $request->get('don_vi');
        $date = $request->get('date');
        $query = LichTruc::with('tinhThanh', 'donViPccc');
        $user = auth()->user();
        if ($user->role_id == 2 && $user->tinh_thanh_id) {
            $query->where('tinh_thanh_id', $user->tinh_thanh_id);
        }
        if (isset($search)) {
            $query->where('tinh_hinh_trong_ngay', 'ilike', $search);
        }
        if (isset($tinh_thanh_id)) {
            $query->where('tinh_thanh_id', $tinh_thanh_id);
        }
        if (isset($don_vi)) {
                $query->whereIn('don_vi_id', $don_vi);
        }
        if (isset($date)) {
            $query->where('ngay_truc', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('ngay_truc', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        $query->orderBy('updated_at', 'desc');
        $lichtruc = $query->paginate($perPage, ['*'], 'page', $page);
        return response(['data' => $lichtruc, 'message' => 'Thành công'], 200);
    }

    public function show($id)
    {
        $truc = LichTruc::with('nhanSus.canBoChienSis', 'phuongTiens.phuongTienPcccs')->where('id', $id)->first();
        return response($truc, 200);
    }

    public function update($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'tinh_thanh_id' => 'required',
            'don_vi_id' => 'required',
            'ngay_truc' => 'required',
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
        $data['ngay_truc'] = Carbon::parse($data['ngay_truc']);
        DB::beginTransaction();
        try {
            $lichTruc = LichTruc::find($id)->update([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'don_vi_id' => $data['don_vi_id'],
                'ngay_truc' => $data['ngay_truc'],
                'chi_huy_doi' => $data['chi_huy_doi'],
                'can_bo_tong_hop' => $data['can_bo_tong_hop'],
                'tong_cbcs' => $data['tong_cbcs'],
                'co_mat' => $data['co_mat'],
                'hanh_chinh' => $data['hanh_chinh'],
                'vang_mat' => $data['vang_mat'],
                'tinh_hinh_trong_ngay' => $data['tinh_hinh_trong_ngay'],
            ]);
            NhanSuPhuongTienTruc::where('truc_id', $id)->delete();

            if (count($data['phuong_tien_pccc_id']) > 0) {
                foreach ($data['phuong_tien_pccc_id'] as $pt) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $id,
                        'type' => 'phuong_tien',
                        'reference_id' =>  $pt,
                        'chi_tiet_nhan_su' => null
                    ]);
                }
            }
            if (count($data['truc_ban']) > 0) {
                foreach ($data['truc_ban'] as $tb) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $id,
                        'type' => 'cbcs',
                        'reference_id' =>  $tb,
                        'chi_tiet_nhan_su' => 'truc_ban'
                    ]);
                }
            }
            if (count($data['truc_chi_huy']) > 0) {
                foreach ($data['truc_chi_huy'] as $tth) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $id,
                        'type' => 'cbcs',
                        'reference_id' =>  $tth,
                        'chi_tiet_nhan_su' => 'truc_chi_huy'
                    ]);
                }
            }
            if (count($data['truc_kiem_tra']) > 0) {
                foreach ($data['truc_kiem_tra'] as $tkt) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $id,
                        'type' => 'cbcs',
                        'reference_id' =>  $tkt,
                        'chi_tiet_nhan_su' => 'truc_kiem_tra'
                    ]);
                }
            }
            if (count($data['truc_thong_tin']) > 0) {
                foreach ($data['truc_thong_tin'] as $ttt) {
                    NhanSuPhuongTienTruc::create([
                        'truc_id' => $id,
                        'type' => 'cbcs',
                        'reference_id' =>  $ttt,
                        'chi_tiet_nhan_su' => 'truc_thong_tin'
                    ]);
                }
            }
            DB::commit();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'data' => $e,
                'message' => 'Không thể cập nhật',
                'code' => 500,
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            LichTruc::find($id)->delete();
            NhanSuPhuongTienTruc::where('truc_id', $id)->delete();
            return response(['message' => 'Xóa thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa', 'data' => $e], 500);
        }
    }
}
