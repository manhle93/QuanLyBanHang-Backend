<?php

namespace App\Http\Controllers;

use App\DonViPccc;
use Illuminate\Http\Request;
use Validator;

class DonViController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = DonViPccc::query()->with('phuongTien', 'phuongTien.donViPccc', 'phuongTien.loaiPhuongTienPccc');
        $search = $request->get('search');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        if (isset($search)) {
            $search = trim($search);
            $query->where('ma', 'ilike', "%{$search}%");
            $query->orWhere('ten', 'ilike', "%{$search}%");
            $query->orWhere('so_dien_thoai', 'ilike', "%{$search}%");
        }
        if (isset($tinh_thanh_id)) {
            $query->where('tinh_thanh_id', $tinh_thanh_id);
        }
        $query->orderBy('updated_at', 'desc');
        $query->with(['tinhThanh', 'quanHuyen']);
        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $users,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function getAll()
    {
    }

    public function list(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        if (!empty($tinh_thanh_id)) {
            $data = DonViPccc::query()->where('tinh_thanh_id', $tinh_thanh_id)->with(['tinhThanh', 'quanHuyen'])->get();
        } else {
            $data = DonViPccc::query()->with(['tinhThanh', 'quanHuyen'])->get();
        }

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function search(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $search = $request->get('query1');
        $tinh_thanh = $request->get('query2');
        if (!empty($tinh_thanh_id)) {
            $data = DonViPccc::query()->where('tinh_thanh_id', $tinh_thanh_id);
        } else {
            if (!empty($tinh_thanh)) {
                $data = DonViPccc::query()->with('tinhThanh')->where('tinh_thanh_id', $tinh_thanh);
            } else {
                $data = DonViPccc::query()->with('tinhThanh');
            }
        }
        if (!empty($search)) {
            $search = trim($search);
            $data->where('ten', 'ilike', "%{$search}%");
            $data->orWhere('search', 'ilike', "%{$search}%");

            $d = $data->get();
        } else {
            $d = [];
        }
        $d = $data->get();

        return response()->json([
            'data' => $d,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function show($id)
    {
        $donvi = DonViPccc::find($id);

        return response()->json([
            'data' => $donvi,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $data['search'] = convert_vi_to_en($request->ten);
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'so_dien_thoai' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm đơn vị'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $data['ma'] = trim($data['ma']);
            if(DonViPccc::where('ma','ilike',$data['ma'])->first()){
                return response()->json([
                    'message' => "Mã đơn vị đã tồn tại",
                    'code' => 400,
                    'data' => ''
                ],400);
            }
            if (!empty($user->tinh_thanh_id)) {
                $data['tinh_thanh_id'] = $user->tinh_thanh_id;
            }
            $user = DonViPccc::create($data);

            return response()->json([
                'message' => 'Thành công',
                'data' => $user,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo đơn vị',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $data['search'] = convert_vi_to_en($request->ten);
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'so_dien_thoai' => 'required',
            'lat' => 'required',
            'long' => 'required',
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
            $data['ma'] = trim($data['ma']);
            if (DonViPccc::where('ma','ilike', $data['ma'])->where('id', '<>',$id)->first()){
                return response(['message' => 'Mã đơn vị đã tồn tại'], 400);
            }
            DonViPccc::where('id', $id)->first()->update($data);

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
            DonViPccc::find($id)->delete();

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

    public function uploadAvatar(Request $request,$id)
    {
        if ($request->file) {
            $image = $request->file;
            $name = time() . '.' . $image->getClientOriginalExtension();
            $image->move('storage/images/avatar/', $name);
            $donvi = DonViPccc::find($id);
            $donvi->update(['avatar_url' => 'storage/images/avatar/' . $name]);
            return 'storage/images/avatar/' . $name;
        }
    }
}
