<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Company;
use App\Http\Controllers\Controller;
use App\Service;
use Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $query = Company::where('id', $user->company_id)->first();
        $company['service'] = Service::where('id', $query->service_id)->get();
        $company['data'] = Company::where('id', $user->company_id)->with('employees')->get();

        return response()->json([
           'data' => $company,
           'message' => 'Lấy dữ liệu thành công',
           'code' => 200,
       ], 200);
    }

    public function detail()
    {
        $listCompany = Company::all();

        return response()->json([
           'data' => $listCompany,
           'message' => 'Lấy dữ liệu thành công',
           'code' => 200,
       ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể cập nhật thông tin công ty'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            Company::where('id', $id)->update(
                [
                    "name"=>$data['name'],
                    "code"=>$data['code'],
                    "service_id"=>$data['service_id'],
                    "description"=>$data['description']
                ]
            );

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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
    }
}
