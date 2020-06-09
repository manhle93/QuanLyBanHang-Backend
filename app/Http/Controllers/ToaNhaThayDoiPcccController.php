<?php

namespace App\Http\Controllers;

use App\ToaNhaThayDoiPccc;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

class ToaNhaThayDoiPcccController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'toa_nha_id'  => 'required',
            'noi_dung'  => 'required',
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
        if (isset($data['thoi_gian'])) {
            $data['thoi_gian'] =  Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
        }
        try {
            $td =  ToaNhaThayDoiPccc::create([
                'toa_nha_id' => $data['toa_nha_id'],
                'noi_dung' => $data['noi_dung'],
                'ghi_chu' => $data['ghi_chu'],
                'thoi_gian' => $data['thoi_gian']
            ]);
            $files = $data['fileList'];
            foreach ($files as $item) {
                if (!empty($item['response']['result'])) {
                    \App\File::where('id', $item['response']['result'])->update(['reference_id' => $td->id]);
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

    /**
     * Display the specified resource.
     *
     * @param  \App\ToaNhaThayDoiPccc  $toaNhaThayDoiPccc
     * @return \Illuminate\Http\Response
     */
    public function show(ToaNhaThayDoiPccc $toaNhaThayDoiPccc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ToaNhaThayDoiPccc  $toaNhaThayDoiPccc
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $data = $request->only('id', 'toa_nha_id', 'noi_dung', 'thoi_gian', 'ghi_chu');
        $validator = Validator::make($data, [
            'toa_nha_id'  => 'required',
            'noi_dung'  => 'required',
            'id' => 'required'
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
        if (isset($data['thoi_gian'])) {
            $data['thoi_gian'] =  Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
        }
        try {
            ToaNhaThayDoiPccc::where('id', $data['id'])->update($data);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ToaNhaThayDoiPccc  $toaNhaThayDoiPccc
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ToaNhaThayDoiPccc $toaNhaThayDoiPccc)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ToaNhaThayDoiPccc  $toaNhaThayDoiPccc
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            ToaNhaThayDoiPccc::find($id)->delete();
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
}
