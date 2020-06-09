<?php

namespace App\Http\Controllers;

use App\DonViPccc;
use App\Http\Requests\PhuongTienPcccRequest;
use App\Http\Resources\PhuongTienPcccResource;
use App\PhuongTienPccc;
use App\TinhThanh;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PhuongTienPcccController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $per_page = $request->query('per_page', 10);
        $tinhThanh = $request->query('tinh_thanh');
        $don_vi = $request->query('don_vi');
        $soHieu = $request->query('so_hieu');
        $bienSo = $request->query('bien_so');
        $query = PhuongTienPccc::whereHas('donViPccc')->latest();
        $soHieu = $request->query('so_hieu');
        if ($tinhThanh)
            $query = TinhThanh::find($tinhThanh)->phuongTienPccc();
        if ($don_vi)
            $query = $query->where('phuong_tien_pcccs.don_vi_pccc_id', $don_vi);
        if ($soHieu)
            $query = $query->where('phuong_tien_pcccs.so_hieu', 'like', '%' . $soHieu . '%');
        if ($bienSo)
            $query = $query->where('phuong_tien_pcccs.bien_so', 'like', '%' . $bienSo . '%');
        if ($request->page) {
            $query = $query->with('loaiPhuongTienPccc', 'donViPccc')->paginate($per_page);
        } else {
            $query = $query->get();
            $objs = [];
            try {
                $json = file_get_contents('http://171.244.50.248:22186/GetAllDeviceLocation/1b680be6-e3eb-4a2f-bdba-751c64595c07');
                $objs = collect(json_decode($json));
            } catch (\Exception $e) {
                $objs = [];
            }
            // return ['data' => $objs->where('IMEI', '864403042860636')->first()];
            if (!empty($objs))
                $query->each(function ($item) use ($objs) {
                    $search = $objs->where('IMEI', $item->imei)->first();
                    if ($search) {
                        $item['lat'] = $search->Latitude;
                        $item['long'] = $search->Longitude;
                    }
                });
        }
        return PhuongTienPcccResource::collection($query);
    }

    public function store(PhuongTienPcccRequest $request)
    {
        if (PhuongTienPccc::where('imei', $request->imei)->first() && isset($request->imei)) {
            return response(['message' => 'IMEI này đã tồn tại'], 400);
        }
        if (PhuongTienPccc::where('bien_so', 'ilike', $request->bien_so)->first()) {
            return response(['message' => 'Biển số xe đã tồn tại'], 400);
        }
        PhuongTienPccc::create($request->all());
        return response('created', Response::HTTP_CREATED);
    }

    public function update(PhuongTienPcccRequest $request, PhuongTienPccc $phuongTienPccc)
    {
        if (PhuongTienPccc::where('imei', $request->imei)->where('id', '<>', $phuongTienPccc->id)->first() && isset($request->imei))
            return response(['message' => 'IMEI này đã tồn tại !'], 400);
        if (PhuongTienPccc::where('bien_so', 'ilike', $request->bien_so)->where('id', '<>', $phuongTienPccc->id)->first()) {
            return response(['message' => 'Biển số xe đã tồn tại'], 400);
        }
        $phuongTienPccc->update($request->all());
        return response('updated', Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CamBien  $qlCamBien
     * @return \Illuminate\Http\Response
     */
    public function destroy(PhuongTienPccc $phuongTienPccc)
    {
        try {
            $phuongTienPccc->delete();
            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response(['message' => 'Bạn không thể xóa phương tiện này'], 400);
        }
    }
    public function getDonViPccc()
    {
        return ['data' => DonViPccc::select('id', 'ten', 'tinh_thanh_id')->get()];
    }

    function getViTriPhuongTienPccc($imei)
    {
        try {
            $data = file_get_contents('http://171.244.50.248:22186/GetLastLocation/1b680be6-e3eb-4a2f-bdba-751c64595c07/' . $imei);
            $data = json_decode($data);
            $huong = $this->xoayXe($imei);
            return ['data' => [
                'lat' => $data->Latitude,
                'long' => $data->Longitude,
                'huong' => $huong
            ]];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function xoayXe($imei)
    {
        $xe = PhuongTienPccc::where('imei', $imei)->first();
        if (!$xe) {
            return 0;
        }
        $huongCu = $xe->huong;
        $lat1 = $xe->lat;
        $lng1 = $xe->long;

        $data = file_get_contents('http://171.244.50.248:22186/GetLastLocation/1b680be6-e3eb-4a2f-bdba-751c64595c07/' . $imei);
        $data = json_decode($data);
        $lat2 = $data->Latitude;
        $lng2 = $data->Longitude;
        if(!$lat1 || !$lng1){{
            $xe->update(['lat' => $lat2, 'long' => $lng2]);
            return 0;
        }}
        if($lat1 == $lat2 && $lng2 == $lng1){
            return $huongCu;
        }

        $y = sin($lng2 - $lng1) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($lng2 - $lng1);
        $z = atan2($y, $x);
        $huongMoi = ($z * 180 / pi() + 360) % 360;
        $xe->update(['lat' => $lat2, 'long' => $lng2, 'huong' => $huongMoi]);
        return $huongMoi;
    }
}
