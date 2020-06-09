<?php

namespace App\Http\Controllers;

use App\HuanLuyenBoiDuong;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;

class HuanLuyenBoiDuongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $user = auth()->user();
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $toa_nha_id = $request->get('toa_nha_id');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $thoi_gian = $request->get('thoi_gian');
        $kiem_tra = HuanLuyenBoiDuong::with('toaNha');
        if (isset($user->tinh_thanh_id)) {
            $kiem_tra->where('tinh_thanh_id', $user->tinh_thanh_id);
        };

        if (isset($search)) {
            $search = trim($search);
            $kiem_tra->where(function ($kiem_tra) use ($search) {
                $kiem_tra->where('noi_dung', 'ilike', "%{$search}%");
            });
        }
        if (isset($toa_nha_id)) {
            $kiem_tra->where('toa_nha_id', $toa_nha_id);
        }
        if (isset($tinh_thanh_id)) {
            $kiem_tra->where('tinh_thanh_id', $tinh_thanh_id);
        };

        if (isset($thoi_gian)) {
            $kiem_tra->where('thoi_gian', '>=', Carbon::parse($thoi_gian[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('thoi_gian', '<=', Carbon::parse($thoi_gian[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        $kiem_tra->orderBy('updated_at', 'desc');
        $data = $kiem_tra->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $data['thoi_gian'] =  Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
        $validator = Validator::make($data, [
            'tinh_thanh_id' => 'required',
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
            HuanLuyenBoiDuong::create([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'thoi_gian' => $data['thoi_gian'],
                'so_giay_cn' => $data['so_giay_cn'],
                'thoi_luong' => $data['thoi_luong'],
                'noi_dung' => $data['noi_dung'],
                'pccc_co_so' => $data['pccc_co_so'],
                'quan_ly_lanh_dao' => $data['quan_ly_lanh_dao'],
                'nguoi_lao_dong' => $data['nguoi_lao_dong'],
                'doi_tuong_khac' => $data['doi_tuong_khac'],
                'toa_nha_id' => $data['toa_nha_id'],
            ]);
            return response()->json([
                'message' => 'Thêm mới thành công',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e,
                'message' => 'Không thể thêm mới',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HuanLuyenBoiDuong  $huanLuyenBoiDuong
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = HuanLuyenBoiDuong::where('id', $id)->first();
        return response($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HuanLuyenBoiDuong  $huanLuyenBoiDuong
     * @return \Illuminate\Http\Response
     */
    public function edit(HuanLuyenBoiDuong $huanLuyenBoiDuong)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HuanLuyenBoiDuong  $huanLuyenBoiDuong
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $data = $request->all();
        $data['thoi_gian'] =  Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
        $validator = Validator::make($data, [
            'tinh_thanh_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể cập nhật mới'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            HuanLuyenBoiDuong::where('id', $id)->first()->update([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'thoi_gian' => $data['thoi_gian'],
                'so_giay_cn' => $data['so_giay_cn'],
                'noi_dung' => $data['noi_dung'],
                'thoi_luong' => $data['thoi_luong'],
                'pccc_co_so' => $data['pccc_co_so'],
                'quan_ly_lanh_dao' => $data['quan_ly_lanh_dao'],
                'nguoi_lao_dong' => $data['nguoi_lao_dong'],
                'doi_tuong_khac' => $data['doi_tuong_khac'],
                'toa_nha_id' => $data['toa_nha_id'],
            ]);
            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e,
                'message' => 'Không thể Cập nhật',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HuanLuyenBoiDuong  $huanLuyenBoiDuong
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            HuanLuyenBoiDuong::find($id)->delete();
            return response()->json([
                'message' => 'Xóa thành công',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e,
                'message' => 'Không thể xóa',
            ], 500);
        }
    }
}
