<?php

namespace App\Http\Controllers;

use App\CamBien;
use App\DanhMuc;
use App\DonViPccc;
use App\Http\Requests\CamBienRequest;
use App\Http\Requests\ThietBiRequest;
use App\Http\Resources\ThietBiResource;
use App\ThietBi;
use App\ThongBaoTrangThaiThietBi;
use App\TinhThanh;
use App\ToaNha;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Excel;
use App\Traits\ExecuteExcel;
use Carbon\Carbon;

class ThietBiController extends Controller
{
    use ExecuteExcel;
    public function index(Request $request)
    {
        $per_page = $request->query('per_page', 5);
        $tinhThanh = $request->query('tinh_thanh');
        $toaNha = $request->query('toa_nha');
        $donviPccc = $request->query('don_vi_pccc');
        $date = $request->get('date');
        if ($tinhThanh) {
            $query = TinhThanh::find($tinhThanh)->thietBi();
        } else if ($toaNha) {
            $query = ToaNha::find($toaNha)->thietBi();
        } else if ($donviPccc) {
            $query = DonViPccc::find($donviPccc)->thietBi();
        } else $query = ThietBi::latest();
        $ma = $request->query('ma');
        if ($ma)
            $query = $query->where(function (Builder $query) use ($ma) {
                return $query->where('thiet_bis.ma', 'like', '%' . $ma . '%')
                    ->orWhere('thiet_bis.imei', 'ilike', '%' . $ma . '%')
                    ->orWhere('thiet_bis.ten', 'ilike', '%' . $ma . '%')
                    ->orWhere('thiet_bis.search', 'ilike', '%' . $ma . '%');
            });
        if (isset($date) && isset($tinhThanh)) {
            $query = ThietBi::query()->with('toaNha');
            $query->whereHas('toaNha', function ($query) use ($tinhThanh){
                $query->where('tinh_thanh_id', $tinhThanh);
            });
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        $query = $query->with('camBien', 'toaNha.tinhThanh', 'toaNha.donViPccc', 'loaiThietBi')->paginate($per_page);
        $json = file_get_contents('http://171.244.49.26:2585/SAS/GetDeviceByUser/123456');
        $objs = collect(json_decode($json));
        $query->each(function ($item) use ($objs) {
            $item['online'] = false;
            $thietBi = $objs->where('IMEI', $item->imei)->first();
            if ($thietBi) {
                $item['online'] = true;
                $item['battery'] = $thietBi->Battery;
            }
        });
        //return $objs;
        return ThietBiResource::collection($query);
    }

    public function excel(Request $request)
    {
        $tinhThanh = $request->query('tinh_thanh');
        $toaNha = $request->query('toa_nha');
        $donviPccc = $request->query('don_vi_pccc');
        if ($tinhThanh) {
            $query = TinhThanh::find($tinhThanh)->thietBi();
        } else if ($toaNha) {
            $query = ToaNha::find($toaNha)->thietBi();
        } else if ($donviPccc) {
            $query = DonViPccc::find($donviPccc)->thietBi();
        } else $query = ThietBi::latest();
        $ma = $request->query('ma');
        if ($ma)
            $query = $query->where(function (Builder $query) use ($ma) {
                return $query->where('thiet_bis.ma', 'ilike', '%' . $ma . '%')
                    ->orWhere('thiet_bis.imei', 'ilike', '%' . $ma . '%')
                    ->orWhere('thiet_bis.ten', 'ilike', '%' . $ma . '%')
                    ->orWhere('thiet_bis.search', 'ilike', '%' . $ma . '%');
            });
        $query = $query->with('camBien', 'toaNha.tinhThanh', 'toaNha.donViPccc', 'loaiThietBi')->get();

        $thietbi_array[] = array('Mã', 'Tên', 'IMEI', 'Tỉnh thành', 'Tòa nhà', 'Đơn vị PCCC', 'Loại thiết bị', 'Địa chỉ');
        $query->each(function ($thietBi) use (&$thietbi_array) {
            $thietbi_array[] = array(
                'Mã'  => $thietBi->ma,
                'Tên'   => $thietBi->ten,
                'IMEI'    => $thietBi->imei,
                'Tinh thành'  => $thietBi->toaNha ? $thietBi->toaNha->tinhThanh->name : null,
                'Tòa nhà' => $thietBi->toaNha ? $thietBi->toaNha->ten : null,
                'Đơn vị PCCC' => $thietBi->toaNha ? $thietBi->toaNha->donViPccc->ten : null,
                'Loại thiết bị' => $thietBi->loaiThietBi != null ? $thietBi->loaiThietBi->ten : null,
                'Địa chỉ' => $thietBi->dia_chi,
            );
        });
        Excel::create('Thiết bị', function ($excel) use ($thietbi_array) {
            $excel->setTitle('Thiết bị');
            $excel->sheet('Thiết bị', function ($sheet) use ($thietbi_array) {
                $sheet->fromArray($thietbi_array, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    public function store(ThietBiRequest $request)
    {
        if (ThietBi::where('imei', 'ilike', $request->imei)->first())
            return response(['message' => 'IMEI này đã tồn tại'], 400);
        if (ThietBi::where('ma', 'ilike', $request->ma)->first()) {
            return response()->json([
                'message' => "Mã thiết bị đã tồn tại",
                'code' => 400,
                'data' => ''
            ], 400);
        }

        $thietBi = ThietBi::create($request->all());
        return response($thietBi, Response::HTTP_CREATED);
    }

    public function update(ThietBiRequest $request, ThietBi $thietBi)
    {
        if (ThietBi::where('imei', 'ilike', $request->imei)->where('id', '<>', $thietBi->id)->first()) {
            return response(['message' => 'IMEI này đã tồn tại'], 400);
        }
        if (ThietBi::where('ma', 'ilike', $request->ma)->where('id', '<>', $thietBi->id)->first()) {
            return response(['message' => 'Mã này đã tồn tại'], 400);
        }
        $thietBi->update($request->all());
        return response('updated', Response::HTTP_ACCEPTED);
    }


    public function destroy(ThietBi $thietBi)
    {
        try {
            $thietBi->delete();
            ThongBaoTrangThaiThietBi::where('thiet_bi_id', $thietBi->id)->delete();
            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response(['message' => 'Bạn không thể xóa thiết bị này'], 400);
        }
    }
    public function themCamBien(Request $request, $id)
    {
        CamBien::where('thiet_bi_id', $id)->update(['thiet_bi_id' => null]);
        CamBien::whereIn('id', $request->all())->update(['thiet_bi_id' => $id]);
        return ['message' => 'OK'];
    }
    public function loaiThietBi()
    {
        return ['data' => DanhMuc::where('code', 'LTB')->first()->children()];
    }
    public function toaNha()
    {
        return ['data' => ToaNha::select('id', 'ten')->get()];
    }
    public function camBien($id)
    {
        return ['data' => CamBien::where('thiet_bi_id', null)->orWhere('thiet_bi_id', $id)->get()];
    }
    public function mobile()
    {
        $data = auth()->user()->toaNha->thietBi()->with('camBien', 'toaNha.tinhThanh', 'toaNha.donViPccc', 'loaiThietBi')->get();
        $json = file_get_contents('http://171.244.49.26:2585/SAS/GetDeviceByUser/123456');
        $objs = collect(json_decode($json));
        $data->each(function ($item) use ($objs) {
            $item['online'] = false;
            $thietBi = $objs->where('IMEI', $item->imei)->first();
            if ($thietBi) {
                $item['online'] = true;
                $item['battery'] = $thietBi->Battery;
            }
        });
        return ThietBiResource::collection($data);
    }
}
