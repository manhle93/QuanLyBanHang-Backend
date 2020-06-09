<?php

namespace App\Http\Controllers;

use App\DanhMuc;
use App\Http\Requests\DanhMucRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DanhMucController extends Controller
{
    public function store(DanhMucRequest $request)
    {
        $data = $request->all();
        unset($data['id']);
        DanhMuc::create($data);

        return response('created', Response::HTTP_CREATED);
    }

    public function update(DanhMucRequest $request, DanhMuc $danhMuc)
    {
        $danhMuc->update($request->all());

        return response('updated', Response::HTTP_ACCEPTED);
    }

    public function index()
    {
        // return ['data' => DanhMuc::danhMucCha()->with('children')->get()];
        return ['data' => DanhMuc::has('children')->with('children')->get()];

    }

    public function destroy(DanhMuc $danhMuc)
    {
        try {
            $danhMuc->delete();

            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return ['message' => 'Bạn không thể xóa danh mục này'];
        }
    }

    public function getDanhMucCon(Request $request)
    {
        $ma = $request->query('ma');

        return ['data' => DanhMuc::where('ma', $ma)->first()->children()->get()];
    }

    public function getDanhMucMobile(){
       $camBienID = DanhMuc::where('ma', 'LCB')->first()->id;
       $thietBiID = DanhMuc::where('ma', 'LTB')->first()->id;
       $mayQuayID = DanhMuc::where('ma', 'LMQ')->first()->id;
       $camBien = DanhMuc::where('parent_id', $camBienID)->select('id', 'ten', 'anh_dai_dien')->get();
       $thietBi = DanhMuc::where('parent_id', $thietBiID)->select('id', 'ten', 'anh_dai_dien')->get();
       $mayQuay = DanhMuc::where('parent_id', $mayQuayID)->select('id', 'ten', 'anh_dai_dien')->get();
       return response(['Thiết bị' => $thietBi, 'Cảm biến' => $camBien, 'Máy quay' => $mayQuay], 200);
    }
}
