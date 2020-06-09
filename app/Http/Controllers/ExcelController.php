<?php

namespace App\Http\Controllers;


use App\Traits\ExecuteExcel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use Symfony\Component\HttpFoundation\Response;

class ExcelController extends Controller
{
    use ExecuteExcel;
    function download(){
        $excelFile = public_path() . '/imports/employee.xlsx';
        return response()->download($excelFile);
    }

    public function importEmployee(Request $request){
        $file = $request->file('file');
        if(empty($file)) {
            return response()->json([
                'data' => [],
                'message' => 'Không tìm thấy file',
                'code' => 404,
            ], 404);
        }
        DB::beginTransaction();
        try{
            $file_id=time();
            $fileName = $file_id.'_'. $file->getClientOriginalName();
            $file->storeAs('public/imports/', $fileName);
            if(Storage::exists('public/imports/'.$fileName)){
                \Excel::filter('chunk')->load(storage_path('app/public/imports/'.$fileName))->chunk(200, function($reader) {
                    $reader->each(function ($row)  {
                        $info= $row->all();
                        $employee['name'] =$info['ten'];
                        $employee['phone_number'] =$info['so_dien_thoai'];
                        $employee['email'] =$info['email'];
                        $employee['email'] =$info['email'];
                        $employee['address'] =$info['dia_chi'];
                        $employee['identity_card'] =$info['cmt'];
                        $phongban= Room::query()->where('name',trim($info['phong_ban']))->first();
                        if(isset($phongban)){
                            $employee['room_id'] =$phongban->id;
                        }
                        $chucvu= Position::query()->where('name',trim($info['chuc_vu']))->first();
                        if(isset($phongban)){
                            $employee['position_id'] =$chucvu->id;
                        }
//                        $employee['date_of_birth'] = Carbon::parse($info['sinh_nhat'])->timezone('Asia/Ho_Chi_Minh')->toDateString();
                        $employee['is_inactivity'] = false;
                        $employee['start_work_date'] =  Carbon::now()->toDateString();
                        $currentCheckingCode = auth()->user()->company->employee2s->count();
                        $employee['checking_code'] = sprintf('%04d', $currentCheckingCode + 1);
                        $employee['company_id'] = auth()->user()->company_id;
                        try{
                            Employee::create($employee);
                        }catch (\Exception $exception){
                            return response()->json([
                                'data' => [],
                                'message' => 'Lỗi upload load nhân sự',
                                'code' => 500,
                            ], 500);
                        }
                    });
                });
                DB::commit();
                return response(['message' => 'created'], Response::HTTP_CREATED);
            }
             return response()->json([
                'data' => [],
                'message' => 'Không tìm thấy file',
                'code' => 404,
            ], 404);
        }
        catch(Exception $exception) {

            DB::rollback();
            return response()->json([
                'data' => [],
                'message' => 'Lỗi',
                'code' => 500,
            ], 500);
        }
    }

}
