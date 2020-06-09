<?php

namespace App\Http\Controllers;

use App\DiemChay;
use App\DiemLayNuoc;
use App\DonViPccc;
use App\PhuongTienPccc;
use App\ThietBiQuay;
use App\ToaNha;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    function getData(Request $request)
    {
        $data = [
            'diem_chay' => [],
            'toanhas' => [],
            'nuoc' => [],
            'thietbiquays' => [],
            'xe' => [],
        ];
        $filters = $request->get('filter');
        $polygon = $request->get('polygon');
        if (isset($filters) && !empty($filters)) {
            foreach ($filters as $filter) {
                switch ($filter) {
                    case 'Tòa Nhà':
                        $data['toanhas'] = ToaNha::query()->where('hien_thi_toa_nha', true)->select('id', 'ten', 'ma','dia_chi', 'chu_toa_nha', 'don_vi_pccc_id', 'lat', 'long', 'tinh_thanh_id', 'loai_hinh_so_huu_id')->with('loaiHinhSoHuu:id,ten', 'donViPccc:id,ten', 'soDienThoai')->get();
                        break;
                    case 'Nước':
                        $data['nuoc'] = DiemLayNuoc::query()->where('hien_thi_tren_map', true)->select('id', 'dia_chi', 'don_vi_quan_ly','don_vi_quan_ly_id', 'kha_nang_cap_nuoc_cho_xe', 'description', 'loai', 'lat', 'long', 'ma', 'ten', 'tinh_thanh_id')->get();

                        break;
                    case 'Camera':
                        $data['thietbiquays'] = ThietBiQuay::query()->get();
                        break;
                    case 'Xe':
                        $query = PhuongTienPccc::whereHas('donViPccc')->latest()->with('donViPccc', 'loaiPhuongTienPccc')->get();
                        $objs=[];
                        try{
                            $json = file_get_contents('http://171.244.50.248:22186/GetAllDeviceLocation/1b680be6-e3eb-4a2f-bdba-751c64595c07');
                            $objs= collect(json_decode($json));
                         }catch(\Exception $e){
                           $objs =[];
                         }
                        // return ['data' => $objs->where('IMEI', '864403042860636')->first()];
                        if(!empty($objs)){
                        $query->each(function ($item, $key) use ($objs, &$query) {
                            $search = $objs->where('IMEI', $item->imei)->first();
                            if ($search && $search->Latitude != 0) {
                                $item['lat'] = $search->Latitude;
                                $item['long'] = $search->Longitude;
                            } else
                                $query->pull($key);
                        });
                    }
                        $data['xe'] = $query->values();
                        break;
                    default:
                        break;
                }
            }
        }
        return response()->json([
            'message' => 'Thành công',
            'data' => $data,
            'code' => 200,
        ], 200);
    }

    function getDataPolygon(Request $request){
        $data = [
            'diem_chay' => [],
            'toanhas' => [],
            'nuoc' => [],
            'thietbiquays' => [],
            'xe' => [],
        ];
        $filters = $request->get('filter');
        $polygon = $request->get('polygon');
        $zoomLevel = $request->get('zoomLevel');
        dd($zoomLevel);
        if (isset($filters) && !empty($filters)) {
            foreach ($filters as $filter) {
                switch ($filter) {
                    case 'Tòa Nhà':
                        $data['toanhas'] = ToaNha::query()->where('hien_thi_toa_nha', true)->select('id', 'ten', 'ma','dia_chi', 'chu_toa_nha', 'don_vi_pccc_id', 'lat', 'long', 'tinh_thanh_id', 'loai_hinh_so_huu_id')->with('loaiHinhSoHuu:id,ten', 'donViPccc:id,ten', 'soDienThoai')->get();
                        break;
                    case 'Nước':
                        $data['nuoc'] = DB::select( DB::raw('SELECT * FROM diem_lay_nuocs a WHERE st_intersects(ST_GeomFromGeoJSON(\''.$polygon.'\'), St_setsrid(ST_MakePoint(a.long, a.lat),4326))'));
                    break;
                    case 'Camera':
                        $data['thietbiquays'] = ThietBiQuay::query()->get();
                        break;
                    case 'Xe':
                        $query = PhuongTienPccc::whereHas('donViPccc')->latest()->with('donViPccc', 'loaiPhuongTienPccc')->get();
                        $objs=[];
                        try{
                            $json = file_get_contents('http://171.244.50.248:22186/GetAllDeviceLocation/1b680be6-e3eb-4a2f-bdba-751c64595c07');
                            $objs= collect(json_decode($json));
                         }catch(\Exception $e){
                           $objs =[];
                         }
                        // return ['data' => $objs->where('IMEI', '864403042860636')->first()];
                        if(!empty($objs)){
                        $query->each(function ($item, $key) use ($objs, &$query) {
                            $search = $objs->where('IMEI', $item->imei)->first();
                            if ($search && $search->Latitude != 0) {
                                $item['lat'] = $search->Latitude;
                                $item['long'] = $search->Longitude;
                            } else
                                $query->pull($key);
                        });
                    }
                        $data['xe'] = $query->values();
                        break;
                    default:
                        break;
                }
            }
        }
        return response()->json([
            'message' => 'Thành công',
            'data' => $data,
            'code' => 200,
        ], 200);
    }
}
