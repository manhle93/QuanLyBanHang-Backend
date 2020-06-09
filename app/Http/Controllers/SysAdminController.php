<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;


class SysAdminController extends Controller
{
    public function index(Request $request){
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $search = $request->get('search');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $service_id = $request->get('service_id');
            $query = Company::with(['service','employees']);
            
            if(!empty($service_id)){
                $query->whereIn('service_id',$service_id);
            }
            if(isset($search)){
                $search = trim($search);
                $query->where('name','ilike', "%{$search}%");
                $query->orWhere('code','ilike', "%{$search}%");
            }
            if(!empty($start_date)){
                $query->where('created_at', '>=',Carbon::parse($start_date));
            }
            if(!empty($end_date)){
                $query->where('created_at', '<',Carbon::parse($end_date));
            }    
            $query->orderBy('updated_at', 'desc');
            $companies = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $companies,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    
    public function editService(Request $request, $id){
        $data = $request->all();
        $validator = Validator::make($data, [
            'service_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thay đổi dịch vụ'),
                'data' => [
                    $validator->errors()->all()
                ]
            ],400);
        }
        try {
            User::where('id', $id)->update(
                [
                    "service_id"=>$data['service_id'],
                ]
            );
            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi cập nhật',
                'code' => 500,
                'data'=> $e
            ], 500);
        }
    }
    public function addCompany(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'service_id' => 'required',
            'code' => 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm công ty'),
                'data' => [
                    $validator->errors()->all()
                ]
            ],400);
        }
        try {
            $company = Company::create($data);
            return response()->json([
                'message' => 'Thành công',
                'data' => $company,
                'code' => 200,
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể thêm công ty',
                'code' => 500,
                'data'=> $e
            ], 500);
        }
    }

}
