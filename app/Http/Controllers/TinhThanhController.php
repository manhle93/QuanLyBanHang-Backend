<?php

namespace App\Http\Controllers;

use App\TinhThanh;
use App\Traits\Geocoding;
use Illuminate\Http\Request;
use Validator;


class TinhThanhController extends Controller
{
    use Geocoding;

    function getAddressByLatLong(Request $request)
    {
        $lat = $request->get('lat');
        $long = $request->get('long');
        return $this->getAddressByLatLon($lat, $long);
    }
    function getLatLongByAddressText(Request $request)
    {
        $search = $request->get('search');
        return $this->getLatLonByAddressText($search);
    }

    public function store(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm tỉnh thành'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $data['code'] = trim($data['code']);
            if(TinhThanh::where('code', $data['code'])->first()){
                return response()->json([
                    'message' => "Mã tỉnh đã tồn tại",
                    'code' => 400,
                    'data' => ''
                ],400);
            }
            $tinh = TinhThanh::create($data);
            return response()->json([
                'message' => 'Thành công',
                'data' => $tinh,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo tỉnh thành',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }
    public function index(Request $request)
    {
        if (isset($request->per_page)) {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $query = TinhThanh::query()->select(['id', 'code', 'name']);
            if (isset($search)) {
                $search = trim($search);
                $query->where('code', 'ilike', "%{$search}%");
                $query->orWhere('name', 'ilike', "%{$search}%");
            }
            $query->orderBy('name', 'desc');
            $tinhs = $query->paginate($perPage, ['*'], 'page', $page);
        } else
            $tinhs = TinhThanh::query()->orderBy('name')->select(['id', 'code', 'name'])->get();
        return response()->json([
            'data' => $tinhs,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'code' => 'required',
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Chưa nhập đủ thông tin, không thể cập nhật'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $data['code'] = trim($data['code']);
            if (TinhThanh::where('code', $data['code'])->where('id', '<>',$id)->first()){
                return response(['message' => 'Mã tỉnh đã tồn tại'], 400);
            }
            TinhThanh::where('id', $id)->update([
                'code' => $data['code'],
                'name' => $data['name']
            ]);

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
    public function delete($id)
    {
        try {
            TinhThanh::find($id)->delete();

            return response()->json([
                'message' => 'Xóa tỉnh thành thành công',
                'code' => 200,
                'data' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi, không thể xóa tỉnh thành này',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }
    public function getDonViPccc(TinhThanh $tinhThanh)
    {
        return ['data' => $tinhThanh->donViPccc()->select('id', 'ten')->get()];
    }
}
