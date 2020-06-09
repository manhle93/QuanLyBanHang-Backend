<?php

namespace App\Http\Controllers;

use App\QuanHuyen;
use Illuminate\Http\Request;
use Validator;


class QuanHuyenController extends Controller
{
    public function store(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'code' => 'required',
            'name' => 'required',
            'tinh_thanh_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm quận huyện'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $data['code'] = trim($data['code']);
            if(QuanHuyen::where('code', $data['code'])->first()){
                return response()->json([
                    'message' => "Mã quận, huyện đã tồn tại",
                    'code' => 400,
                    'data' => ''
                ],400);
            }
            $quan = QuanHuyen::create($data);
            return response()->json([
                'message' => 'Thành công',
                'data' => $quan,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo quận huyện',
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
            $tinh_thanh_id = $request->get('tinh_thanh_id');
            $query = QuanHuyen::query()->select(['id','code','name','tinh_thanh_id'])->with('tinhThanh');
            if(isset($search)){
                $search = trim($search);
                $query->where('name','ilike', "%{$search}%");
                $query->orWhere('code','ilike', "%{$search}%");

            }
            $query->orderBy('name', 'desc');
            $quans = $query->paginate($perPage, ['*'], 'page', $page);
        } else{
            $tinh_thanh_id = $request->get('tinh_thanh_id');
            $query = QuanHuyen::query()->select(['id','code','name']);
            if(isset($tinh_thanh_id)){
                $query->where('tinh_thanh_id',$tinh_thanh_id);
            }
            $quans = $query->get();
        }
        return response()->json([
            'data' => $quans,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'code' => 'required',
            'name' => 'required',
            'tinh_thanh_id' => 'required',
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
            if (QuanHuyen::where('code', $data['code'])->where('id', '<>',$id)->first()){
                return response(['message' => 'Mã quận đã tồn tại'], 400);
            }
            QuanHuyen::where('id', $id)->update([
                'code'=>$data['code'],
                'name'=>$data['name'],
                'tinh_thanh_id'=>$data['tinh_thanh_id']
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
            QuanHuyen::find( $id)->delete();
            return response()->json([
                'message' => 'Xóa quận huyện thành thành công',
                'code' => 200,
                'data' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi, không thể xóa quận huyện này',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }
    public function getQuanHuyenTheoTinh(Request $request)
    {
        $data = $request->all();
      
        if(!empty($data['tinh_thanh_id'])){
            $quanhuyens = QuanHuyen::where('tinh_thanh_id', $data['tinh_thanh_id'])->select('id','code','name','tinh_thanh_id')->with('tinhThanh')->get();
        }else{
            
            $quanhuyens = QuanHuyen::query()->select('id','code','name','tinh_thanh_id')->with('tinhThanh')->get();
           
        }
        return response()->json([
            'data' => $quanhuyens,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
}
