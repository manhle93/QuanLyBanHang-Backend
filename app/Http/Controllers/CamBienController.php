<?php

namespace App\Http\Controllers;

use App\CamBien;
use App\Http\Requests\CamBienRequest;
use App\Http\Resources\CamBienResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class CamBienController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->query('per_page', 5);
        $query = CamBien::khuVuc();
        $search = $request->query('search');
        $loai_cam_bien_id = $request->query('loai_cam_bien_id');
        $trang_thai_id = $request->query('trang_thai_id');
        if (isset($search)) {
            $search = trim($search);
            $query->where('ma', 'ilike', "%{$search}%");
            $query->orWhere('IMEI_thiet_bi', 'ilike', "%{$search}%");
        }
        if (isset($loai_cam_bien_id)) {
            $query->where('loai_cam_bien_id', $loai_cam_bien_id);
        }
        if (isset($trang_thai_id)) {
            $query->where('trang_thai_id', $trang_thai_id);
        }

        return CamBienResource::collection($query->with('loaiCamBien', 'trangThai', 'thietBi')->orderBy('updated_at', 'desc')->paginate($per_page));
    }

    public function store(CamBienRequest $request)
    {
        $data = $request->all();
        if (CamBien::where('IMEI_thiet_bi','ilike', $data['IMEI_thiet_bi'])->first()) {
            return response(['message' => 'IMEI này đã tồn tại'], 400);
        }
        $data['ngay_trien_khai'] = Carbon::parse($data['ngay_trien_khai'])->timezone('Asia/Ho_Chi_Minh');
        $data['ngay_het_han'] = Carbon::parse($data['ngay_het_han'])->timezone('Asia/Ho_Chi_Minh');
        $data['ma'] = trim($data['ma']);
        if(CamBien::where('ma','ilike', $data['ma'])->first()){
            return response()->json([
                'message' => "Mã cảm biến đã tồn tại",
                'code' => 400,
                'data' => ''
            ],400);
        }
        CamBien::create($data);

        return response('created', Response::HTTP_CREATED);
    }

    public function update(CamBienRequest $request, $id)
    {
        $data = $request->all();
        if (CamBien::where('IMEI_thiet_bi','ilike', $data['IMEI_thiet_bi'])->where('id', '<>', $id)->first()) {
            return response(['message' => 'IMEI này đã tồn tại'], 400);
        }
        if (CamBien::where('ma','ilike', $data['ma'])->where('id', '<>', $id)->first()) {
            return response(['message' => 'Mã này đã tồn tại'], 400);
        }
        $data['ngay_trien_khai'] = Carbon::parse($data['ngay_trien_khai'])->timezone('Asia/Ho_Chi_Minh');
        $data['ngay_het_han'] = Carbon::parse($data['ngay_het_han'])->timezone('Asia/Ho_Chi_Minh');
        CamBien::find($id)->update($data);

        return response('updated', Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\CamBien $qlCamBien
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(CamBien $camBien)
    {
        try {
            $camBien->delete();

            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response(['message' => 'Bạn không thể xóa loại cảm biến này'], 400);
        }
    }

    public function thietBi()
    {
        return ['data' => ThietBi::where('thiet_bi_id', null)->get()];
    }
}
