<?php

namespace App\Http\Controllers;

use App\ThamDinhPheDuyet;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

class ThamDinhPheDuyetController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'tinh_thanh_id' => 'required',
            'toa_nha_id'  => 'required',
            'ngay_cap'  => 'required',
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
        $data['ngay_cap'] =  Carbon::parse($data['ngay_cap'])->timezone('Asia/Ho_Chi_Minh');
        try {
           $thamduyet = ThamDinhPheDuyet::create([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'toa_nha_id' => $data['toa_nha_id'],
                'ngay_cap' => $data['ngay_cap'],
                'ten_van_ban' => $data['ten_van_ban'],
                'so_van_ban' => $data['so_van_ban'],
                'co_quan_cap' => $data['co_quan_cap'],
                'ghi_chu' => $data['ghi_chu'],
            ]);
            $files = $data['fileList'];
            foreach ($files as $item) {
                if (!empty($item['response']['result'])) {
                    \App\File::where('id', $item['response']['result'])->update(['reference_id' => $thamduyet->id]);
                }
            }
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
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $user = auth()->user();
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $toa_nha_id = $request->get('toa_nha_id');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $ngay_cap = $request->get('ngay_cap');
        $kiem_tra = ThamDinhPheDuyet::query();
        if (isset($user->tinh_thanh_id)) {
            $kiem_tra->where('tinh_thanh_id', $user->tinh_thanh_id);
        };

        if (isset($search)) {
            $search = trim($search);
            $kiem_tra->where(function ($kiem_tra) use ($search) {
                $kiem_tra->where('ten_van_ban', 'ilike', "%{$search}%")
                    ->orWhere('so_van_ban', 'ilike', "%{$search}%")
                    ->orWhere('co_quan_cap', 'ilike', "%{$search}%");
            });
        }
        if (isset($toa_nha_id)) {
            $kiem_tra->where('toa_nha_id', $toa_nha_id);
        }
        if (isset($tinh_thanh_id)) {
            $kiem_tra->where('tinh_thanh_id', $tinh_thanh_id);
        };

        if (isset($ngay_cap)) {
            $kiem_tra->where('ngay_cap', '>=', Carbon::parse($ngay_cap[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('ngay_cap', '<=', Carbon::parse($ngay_cap[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        $kiem_tra->orderBy('updated_at', 'desc');
        $data = $kiem_tra->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
    public function show($id)
    {
        $data = ThamDinhPheDuyet::with('files')->where('id', $id)->first();
        return response($data, 200);
    }

    public function update($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'tinh_thanh_id' => 'required',
            'toa_nha_id'  => 'required',
            'ngay_cap'  => 'required',
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
        $data['ngay_cap'] =  Carbon::parse($data['ngay_cap'])->timezone('Asia/Ho_Chi_Minh');
        try {
            ThamDinhPheDuyet::where('id', $id)->first()->update([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'toa_nha_id' => $data['toa_nha_id'],
                'ngay_cap' => $data['ngay_cap'],
                'ten_van_ban' => $data['ten_van_ban'],
                'so_van_ban' => $data['so_van_ban'],
                'co_quan_cap' => $data['co_quan_cap'],
                'ghi_chu' => $data['ghi_chu'],
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

    public function delete($id)
    {
        try {
            ThamDinhPheDuyet::find($id)->delete();
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
