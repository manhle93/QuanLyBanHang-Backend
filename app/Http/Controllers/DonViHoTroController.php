<?php

namespace App\Http\Controllers;

use App\CamBien;
use App\DanhMuc;
use App\DiemChay;
use App\DonViHoTro;
use App\DonViPccc;
use App\LichSuGoiDien;
use App\Scopes\TinhThanhScope;
use App\Scopes\ToaNhaScope;
use App\SoDienThoaiToaNha;
use App\ThietBi;
use App\ToaNha;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Validator;


class DonViHoTroController extends Controller
{
    function callDiemchay()
    {
        for ($i = 0; $i <= 5; $i++) {

            $this->getThietBi();
            if ($i != 5) {
                sleep(10);
            }
        }
    }

    function create()
    {
        DiemChay::create([
            'ten' => "Rạp xiếc trung ương",
            'so_dien_thoai' => "0974113810",
            'ten_nguoi_bao' => null,
            'toa_nha_id' => 7,
            'dia_chi' => "Lê Đại Hành Hai Bà Trưng Hà Nội",
            'lat' => 21.016893491618,
            'long' => 105.84319918358,
            'tinh_thanh_id' => 23,
            'trang_thai' => 'dang_chay',
            'thoi_gian_bao_chay' => Carbon::now(),
        ]);
    }
    private function getThietBi()
    {
        \Log::error("test");
        $json = file_get_contents('http://171.244.49.26:2585/SAS/GetDeviceByUser/123456');
        $objs = json_decode($json);

        try {
            foreach ($objs as $obj) {
                if ($obj->AlarmState) {
                    $thietbi = ThietBi::query()->withoutGlobalScope(ToaNhaScope::class)->where('imei', $obj->IMEI)->first();
                    $toanha = null;
                    $camBienImeis = [];
                    $cb = null;
                    if ($obj->Sensors) {
                        foreach ($obj->Sensors as $camBien) {
                            if ($camBien->TrangThai == "1") {
                                array_push($camBienImeis, $camBien->IMEI);
                                if (empty($cb)) {
                                    $cb = $camBien->IMEI;
                                }
                            }
                        }
                    }
                    if (isset($thietbi)) {
                        $toanha = ToaNha::query()->withoutGlobalScope(TinhThanhScope::class)->with('soDienThoai')->find($thietbi->toa_nha_id);
                    }
                    if (isset($toanha)) {
                        if (DiemChay::where('toa_nha_id', $toanha->id)->whereIn('trang_thai', ['dang_chay', 'da_xu_ly'])->count() == 0) {
                            $cbien = \App\CamBien::where('IMEI_thiet_bi', $cb)->first();
                            $diemChay = DiemChay::create([
                                'ten' => $toanha->ten,
                                'so_dien_thoai' => $toanha->so_dien_thoai,
                                'ten_nguoi_bao' => null,
                                'toa_nha_id' => $toanha->id,
                                'dia_chi' => $toanha->dia_chi,
                                'lat' => $toanha->lat,
                                'long' => $toanha->long,
                                'tinh_thanh_id' => $toanha->tinh_thanh_id,
                                'trang_thai' => 'dang_chay',
                                'thoi_gian_bao_chay' => Carbon::now(),
                                'IMEI_thiet_bi' => $obj->IMEI ? $obj->IMEI : null,
                                'cam_bien_id' => empty($cbien) ? null : $cbien->id,
                                'cam_bien_tiep_theo' => null
                            ]);
                            $phone = SoDienThoaiToaNha::where('toa_nha_id', $toanha->id)->pluck('so_dien_thoai');
                            $viTri = "";
                            if (count($camBienImeis) > 0) {
                                $diemChay->camBien()->attach(\App\CamBien::whereIn('IMEI_thiet_bi', $camBienImeis)->get()->pluck('id')->all());
                                $vt = CamBien::where('IMEI_thiet_bi', $camBienImeis[0])->first();
                                if ($vt) {
                                    $viTri = $viTri . ", " . $vt->vi_tri;
                                }
                                $viTri = $toanha->ten . ", tại vị trí, " . $viTri;
                                foreach ($phone as $it) {
                                    $this->callServer($it, $viTri, $diemChay->id, $toanha->id);
                                }
                            } else {
                                foreach ($phone as $it) {
                                    $this->callServer($it, $toanha->ten, $diemChay->id, $toanha->id);
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    function callServer($phoneNumber, $viTri, $diemChayID, $toaNhaID)
    {

        if ($phoneNumber[0] == 0) {
            $phoneNumber = ltrim($phoneNumber, '0');
            $phoneNumber = '84' . $phoneNumber;
        }
        $content = "Cảnh báo, Cảnh báo, Tòa nhà " . $viTri . " Đang xảy ra nguy cơ cháy. Các đơn vị kiểm tra xác minh. " . " Cảnh báo, Cảnh báo, Tòa nhà " . $viTri . " Đang xảy ra nguy cơ cháy. Các đơn vị kiểm tra xác minh";
        $call['apiKey'] = '2982#djd838jdh!23';
        $call['toNumber'] = $phoneNumber;
        $call['content'] = $content;
        $call['checksum'] = $call['apiKey'] . $call['toNumber'] . $call['content'];
        $call['checksum'] = md5($call['checksum']);
        $url = 'http://171.244.49.26:1028/CallOut';
        $myvars = json_encode($call);
        if (LichSuGoiDien::where('diem_chay_id', $diemChayID)->where('so_dien_thoai', $phoneNumber)->count() >= 2) {
            return response()->json([
                'message' => 'Đã gọi điện 2 lần.',
            ], 300);
        }

        $callLog = LichSuGoiDien::where('toa_nha_id', $toaNhaID)->where('so_dien_thoai', $phoneNumber)->latest()->first();
        if ($callLog != null && !Carbon::parse($callLog->updated_at)->addMinutes(2)->isPast()) {
            return response()->json([
                'message' => 'Đã thực hiện một cuộc gọi trước đó.',
            ], 300);
        };
        $lan_goi = LichSuGoiDien::where('toa_nha_id', $toaNhaID)->where('so_dien_thoai', $phoneNumber)->count();
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            if (json_decode($response, true)['errorCode'] == 0) {
                LichSuGoiDien::create([
                    'toa_nha_id' => $toaNhaID,
                    'so_dien_thoai' => $phoneNumber,
                    'diem_chay_id' => $diemChayID
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể thực hiện cuộc gọi',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function store(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'loai_don_vi_id' => 'required',
            'so_dien_thoai' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'tinh_thanh_id' => 'required',
            'quan_huyen_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm đơn vị'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $data['ma'] = trim($data['ma']);
            if (DonViHoTro::where('ma', 'ilike', $data['ma'])->first()) {
                return response()->json([
                    'message' => "Mã đơn vị đã tồn tại",
                    'code' => 400,
                    'data' => ''
                ], 400);
            }
            $donvis = DonViHoTro::create([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'loai_don_vi_id' => $data['loai_don_vi_id'],
                'lat' => $data['lat'],
                'long' => $data['long'],
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'quan_huyen_id' => $data['quan_huyen_id'],
            ]);
            return response()->json([
                'message' => 'Thành công',
                'data' => $donvis,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể thêm đơn vị',
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
            $quan_huyen_id = $request->get('quan_huyen_id');
            $tinh_thanh_id = $request->get('tinh_thanh_id');
            $query = DonViHoTro::query()->with('loaiDonVi', 'tinhThanh', 'quanHuyen');
            if (!empty($tinh_thanh_id)) {
                $query->where('tinh_thanh_id', $tinh_thanh_id);
            }
            if (!empty($quan_huyen_id)) {
                $query->where('quan_huyen_id', $quan_huyen_id);
            }
            if (isset($search)) {
                $search = trim($search);
                $query->where(function ($query) use ($search) {
                    $query->where('ma', 'ilike', "%{$search}%")
                        ->orWhere('ten', 'ilike', "%{$search}%");
                });
            }
            $query->orderBy('updated_at', 'desc');
            $donvis = $query->paginate($perPage, ['*'], 'page', $page);
        } else
            $donvis = DonViHoTro::query()->with('loaiDonVi')->get();
        return response()->json([
            'data' => $donvis,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'so_dien_thoai' => 'required',
            'loai_don_vi_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
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
            $data['ma'] = trim($data['ma']);
            if (DonViHoTro::where('ma', 'ilike', $data['ma'])->where('id', '<>', $id)->first()) {
                return response(['message' => 'Mã đơn vị đã tồn tại'], 400);
            }
            DonViHoTro::where('id', $id)->first()->update([
                'ma' => $data['ma'],
                'ten' => $data['ten'],
                'loai_don_vi_id' => $data['loai_don_vi_id'],
                'so_dien_thoai' => $data['so_dien_thoai'],
                'lat' => $data['lat'],
                'long' => $data['long'],
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'quan_huyen_id' => $data['quan_huyen_id'],
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
            DonViHoTro::find($id)->delete();
            return response()->json([
                'message' => 'Xóa thành công',
                'code' => 200,
                'data' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể xóa đơn vị hỗ trợ này',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function getLoaiDonVi()
    {
        $data = DanhMuc::where('parent_id', 7)->get();
        return response()->json([
            'message' => 'lấy dữ liệu thành công',
            'code' => 200,
            'data' => $data,
        ], 200);
    }

    public function show($id)
    {
        $data = DonViHoTro::where('id', $id)->first();
        return response()->json([
            'message' => 'lấy dữ liệu thành công',
            'code' => 200,
            'data' => $data,
        ], 200);
    }
}
