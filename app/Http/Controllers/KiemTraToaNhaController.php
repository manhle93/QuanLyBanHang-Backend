<?php

namespace App\Http\Controllers;

use App\KiemTraToaNha;
use App\TrangThaiKiemTra;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class KiemTraToaNhaController extends Controller
{
    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $data['search'] = convert_vi_to_en($request->can_bo_kiem_tra . $request->quyet_dinh_kiem_tra . $request->thong_tin . $request->danh_gia);
        $validator = Validator::make($data, [
            'danh_gia' => 'required',
            'thong_tin' => 'required',
            'toa_nha_id' => 'required',
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
        try {
            KiemTraToaNha::where('id', $id)->first()->update($data);

            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi cập nhật',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function show($id)
    {
        $kt = KiemTraToaNha::with('files')->find($id);

        return response()->json([
            'data' => $kt,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $info = $request->all();
        $info['search'] = convert_vi_to_en($request->can_bo_kiem_tra . $request->quyet_dinh_kiem_tra . $request->thong_tin . $request->danh_gia);
        $validator = Validator::make($info, [
            'danh_gia' => 'required',
            'thong_tin' => 'required',
            'toa_nha_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm kiểm tra tòa nhà'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $files = $info['fileList'];
            $kttoanha = KiemTraToaNha::create($info);

            foreach ($files as $item) {
                if (!empty($item['response']['result'])) {
                    \App\File::where('id', $item['response']['result'])->update(['reference_id' => $kttoanha->id]);
                }
            }

            return response()->json([
                'message' => 'Thành công',
                'data' => $kttoanha,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Lỗi ! Không thể tạo kiểm tra tòa nhà',
            ]);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $user = auth()->user();
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $toa_nha_id = $request->get('toa_nha_id');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $ngay_kiem_tra = $request->get('ngay_kiem_tra');
        $kiem_tra = KiemTraToaNha::query()->with('toaNha');
        if (isset($user->tinh_thanh_id)) {
            $kiem_tra->whereHas('toaNha', function ($query) use ($user) {
                $query->where('tinh_thanh_id', $user->tinh_thanh_id);
            });
        }
        if (isset($search)) {
            $search = trim($search);
            $kiem_tra->where(function ($kiem_tra) use ($search) {
                $kiem_tra->where('can_bo_kiem_tra', 'ilike', "%{$search}%");
                $kiem_tra->orWhere('danh_gia', 'ilike', "%{$search}%");
                $kiem_tra->orWhere('thong_tin', 'ilike', "%{$search}%");
                $kiem_tra->orWhere('quyet_dinh_kiem_tra', 'ilike', "%{$search}%");
                $kiem_tra->orWhere('search', 'ilike', "%{$search}%");
            });
        }
        if (isset($toa_nha_id)) {
            $kiem_tra->where('toa_nha_id', $toa_nha_id);
        }
        if (isset($tinh_thanh_id)) {
            $kiem_tra->whereHas('toaNha', function ($query) use ($tinh_thanh_id) {
                $query->where('tinh_thanh_id', $tinh_thanh_id);
            });
        }
        if (isset($ngay_kiem_tra)) {
            $kiem_tra->where('ngay_kiem_tra', '>=', Carbon::parse($ngay_kiem_tra[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('ngay_kiem_tra', '<=', Carbon::parse($ngay_kiem_tra[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        $kiem_tra->orderBy('updated_at', 'desc');
        $data = $kiem_tra->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
    public function getTrangThaiKiemTra()
    {
        $trangthai =  TrangThaiKiemTra::get();
        return $trangthai;
    }
    public function delete($id)
    {
        try {
            KiemTraToaNha::find($id)->delete();

            return response()->json([
                'message' => 'Xóa thành công',
                'code' => 200,
                'data' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi, không thể xóa đơn vị',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }
}
