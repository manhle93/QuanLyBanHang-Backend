<?php

namespace App\Http\Controllers;

use App\CamBien;
use App\CamBienDiemChay;
use App\CTDonViHoTroDiemChay;
use App\DonViPccc;
use App\Scopes\TinhThanhScope;
use App\TinhThanh;
use App\ToaNha;
use Illuminate\Http\Request;
use App\DiemChay;
use App\DonViHoTro;
use App\PhuongTienPccc;
use App\CTPhuongTienPcccDiemChay;
use App\LichSuGoiDien;
use App\Scopes\ToaNhaScope;
use App\SoDienThoaiToaNha;
use App\ThietBi;
use Validator;
use DB;
use Excel;
use Carbon\Carbon;
use App\Traits\ExecuteExcel;

class DiemChayController extends Controller
{
    use ExecuteExcel;
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $diemchay = DiemChay::where('toa_nha_id', $user->toa_nha_id)->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'data' => $diemchay,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200'
        ], 200);
    }

    public function dataPaginate(Request $request)
    {
        $user = auth()->user();
        $ma_user = $user->tinh_thanh_id;
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = DiemChay::query()->whereHas('tinhThanh')->with(['trangThaiDiemChay', 'toaNha', 'tinhThanh']);
        $search = $request->get('search');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $year = $request->get('year');
        $don_vi_pccc_id = $request->get('don_vi_pccc_id');
        $date = $request->get('date');
        if (isset($year)) {
            $query->whereYear('created_at', $year);
        }
        if (isset($tinh_thanh_id)) {
            $query->where('tinh_thanh_id', $tinh_thanh_id);
        }
        if (isset($don_vi_pccc_id)) {
            $query->whereHas('phuongTienPccc', function ($query) use ($don_vi_pccc_id) {
                $query->whereHas('donViPccc', function ($query) use ($don_vi_pccc_id) {
                    $query->where('id', $don_vi_pccc_id);
                });
            });
        }
        if (isset($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('ten', 'ilike', "%{$search}%");
                $query->orWhere('so_dien_thoai', 'ilike', "%{$search}%");
                $query->orWhere('ten_nguoi_bao', 'ilike', "%{$search}%");
            });
        }
        $query->orderBy('updated_at', 'desc');
        $diemchay = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $diemchay,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200'
        ], 200);
    }

    public function show($id)
    {
        $donvi = DiemChay::with(['phuongTienPccc.loaiPhuongTienPccc', 'phuongTienPccc.donViPccc', 'donViHoTro.loaiDonVi'])->find($id);
        return response()->json([
            'data' => $donvi,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function getData(Request $request)
    {
        $diemchay = DiemChay::query()->with('toaNha', 'toaNha.soDienThoai');
        $trangThai = $request->query('trang_thai', 'dang_chay');
        $data = $diemchay->where('trang_thai', $trangThai)->with('toaNha', 'camBienFirst', 'donViHoTro', 'phuongTienPccc', 'camBien')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200'
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $data['trang_thai'] = 'dang_chay';
        $data['thoi_gian_bao_chay'] = Carbon::now();
        if (!empty($user->tinh_thanh_id)) {
            $data['tinh_thanh_id'] = $user->tinh_thanh_id;
        }
        $data['ma'] = DiemChay::query()->count();
        $validator = Validator::make($data, [
            'ten' => 'required',
            'so_dien_thoai' => 'required',
            'dia_chi' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm điểm cháy'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $diemchay = DiemChay::create($data);

            return response()->json([
                'message' => 'Thành công',
                'data' => $diemchay,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo tòa điểm cháy',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            // 'so_dien_thoai' => 'required',
            'dia_chi' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể chỉnh sửa điểm cháy'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }

        try {
            if (isset($data['thoi_gian_bao_chay']) && $data['thoi_gian_bao_chay'] != null) {
                $data['thoi_gian_bao_chay'] = Carbon::parse($data['thoi_gian_bao_chay'])->timezone('Asia/Ho_Chi_Minh');
            };
            if (isset($data['thoi_gian_bat_dau_xu_ly']) && $data['thoi_gian_bat_dau_xu_ly'] == 'bat_dau_xu_ly_chay') {
                $data['thoi_gian_bat_dau_xu_ly'] = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
            }
            if (isset($data['thoi_gian_ket_thuc']) && $data['thoi_gian_ket_thuc'] == 'ket_thuc_xu_ly_chay') {
                $data['thoi_gian_ket_thuc'] = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
            }
            if (isset($data['thoi_gian_bat_dau_xu_ly']) && $data['thoi_gian_bat_dau_xu_ly'] != null && $data['thoi_gian_bat_dau_xu_ly'] != 'bat_dau_xu_ly_chay') {
                $data['thoi_gian_bat_dau_xu_ly'] = Carbon::parse($data['thoi_gian_bat_dau_xu_ly'])->timezone('Asia/Ho_Chi_Minh');
            };
            if (isset($data['thoi_gian_ket_thuc']) && $data['thoi_gian_ket_thuc'] != null && $data['thoi_gian_ket_thuc'] != 'ket_thuc_xu_ly_chay') {
                $data['thoi_gian_ket_thuc'] = Carbon::parse($data['thoi_gian_ket_thuc'])->timezone('Asia/Ho_Chi_Minh');
            };
            $diemchay = DiemChay::find($id);
            $diemchay->update($data);
            $donvi = null;
            if (isset($diemchay->toa_nha_id)) {
                $toanha = ToaNha::find($diemchay->toa_nha_id);
                $donvi = DonViPccc::where('id', $toanha->don_vi_pccc_id)->first();
            }
            if (!isset($donvi)) {
                $donvi = DonViPccc::where('loai_hinh_don_vi', 'nghiep_vu')->orderBy((DB::raw("
                    ST_DISTANCE(
                        Geography(ST_SetSRID(ST_MakePoint(" . $diemchay->long . ", " . $diemchay->lat . "),4326)),
                        Geography(ST_SetSRID(ST_MakePoint(long, lat),4326)))
                    ")), 'asc')->first();
            }
            return response()->json([
                'message' => 'Thành công',
                'data' => ["diem_chay" => $diemchay, "donvi" => $donvi],
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể chỉnh sửa  điểm cháy',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }
    public function addPhuongTienPccc(DiemChay $diemChay, Request $request)
    {
        $diemChay->phuongTienPccc()->attach($request->query('id'));
    }
    public function donViHoTro(DiemChay $diemChay, Request $request)
    {
        if ($request->query('mode') == 'add') {
            $diemChay->donViHoTro()->attach($request->query('id'));
        } else $diemChay->donViHoTro()->detach($request->query('id'));
    }
    public function phuongTienPccc(DiemChay $diemChay, Request $request)
    {
        if ($request->query('mode') == 'add')
            $diemChay->phuongTienPccc()->attach($request->query('id'));
        else $diemChay->phuongTienPccc()->detach($request->query('id'));
    }
    public function getDonViHoTro($id)
    {
        $query = DonViHoTro::query();
        $tinhThanh = auth()->user()->tinh_thanh_id;
        $idDvhtAcs = CTDonViHoTroDiemChay::where('diem_chay_id', $id)->pluck('don_vi_ho_tro_id');
        if (isset($tinhThanh)) {
            $data = $query->where('tinh_thanh_id', $tinhThanh)->get();
        } else $data = $query->get();
        foreach ($data as $item) {
            if (in_array($item['id'], $idDvhtAcs->toArray()))
                $item['status'] = true;
            else
                $item['status'] = false;
        }
        return ['data' => $data];
    }
    public function getPhuongTienPccc($id)
    {
        $query = PhuongTienPccc::query();
        $donviIds = null;
        if (isset(auth()->user()->tinh_thanh_id))
            $donviIds = DonViPccc::query()->pluck('id');

        $idDiemchays = DiemChay::query()->withoutGlobalScope(TinhThanhScope::class)->whereNotIn('trang_thai', ['canh_bao_sai', 'ket_thuc_xu_ly'])->where('id', '<>', $id)->pluck('id');
        $donviQuery = CTPhuongTienPcccDiemChay::query();
        $idDvhts = $donviQuery->whereIn('diem_chay_id', $idDiemchays)->pluck('phuong_tien_pccc_id');
        $idDvhtAcs = CTPhuongTienPcccDiemChay::where('diem_chay_id', $id)->pluck('phuong_tien_pccc_id');
        if (isset($donviIds)) {
            $data = $query->whereIn('don_vi_pccc_id', $donviIds)->whereNotIn('id', $idDvhts)->get();
        } else $data = $query->whereNotIn('id', $idDvhts)->get();
        foreach ($data as $item) {
            if (in_array($item['id'], $idDvhtAcs->toArray()))
                $item['status'] = true;
            else
                $item['status'] = false;
        }
        return ['data' => $data];
    }

    public function export(Request $request)
    {
        $user =  auth()->user();
        $search_time_start = $request->get('search_time_start');
        $search_time_end = $request->get('search_time_end');

        $excelFile = public_path() . '/exports/danh_sach_diem_chay.xlsx';
        $this->load($excelFile, 'Sheet1', function ($excel) use ($user) {
        })->download('xlsx');
    }
    public function excel()
    {
        // $customer_data = DB::table('diem_chays')->get()->toArray();
        $diemchay_data = DiemChay::query()->whereHas('tinhThanh')->with(['trangThaiDiemChay', 'toaNha', 'tinhThanh'])->get();
        $diemchay_array[] = array('STT', 'Tên', 'Số điện thoại', 'Người báo', 'Địa chỉ', 'Tòa Nhà', 'Nguyên nhân', 'Ước tính thiệt hại', 'Số người chết', 'Số người bị thương', 'Ghi chú');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Tên'  => $diemchay->ten,
                'Số điện thoại'   => $diemchay->so_dien_thoai,
                'Người báo'    => $diemchay->ten_nguoi_bao,
                'Địa chỉ'  => $diemchay->dia_chi,
                'Tòa Nhà' => $diemchay->toaNha['ten'],
                'Nguyên nhân' => $diemchay->nguyen_nhan,
                'Ước tính thiệt hại' => $diemchay->uoc_tinh_thiet_hai,
                'Số người chết' => str_replace(' ', '', $diemchay->so_nguoi_chet),
                'Số người bị thương' => str_replace(' ', '', $diemchay->so_nguoi_bi_thuong),
                'Ghi chú' => $diemchay->mo_ta
            );
        }
        // $excelFile = public_path() . '/exports/danh_sach_diem_chay.xlsx';
        // $this->load($excelFile, 'Sheet1', function ($excel) use ($diemchay_array) {
        //     $excel->sheet('Sheet1', function ($sheet) use ($diemchay_array) {
        //         $sheet->fromArray($diemchay_array, null, 'A1', false, false);
        //     });
        // })->download('xlsx');

        Excel::create('Điểm cháy', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Điểm cháy');
            $excel->sheet('Điểm cháy', function ($sheet) use ($diemchay_array) {
                $sheet->fromArray($diemchay_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($diemchay_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));

                    if ($i == 0) {
                        foreach ($diemchay_array as $key => $diemchay) {
                            $sheet->getStyle($j . (++$key))->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );
                        }
                    }
                    $j++;
                }
                #Using HT
            });
        })->download('xlsx');
    }

    function checkDiemChay($id)
    {
        $i = DiemChay::query()->where('id', $id)->count();
        return ['data' => $i];
    }
    function goiDien(Request $request)
    {
        $data = $request->all();
        $call['apiKey'] = $request->get('apiKey');
        $call['toNumber'] = $request->get('toNumber');
        $call['content'] = $request->get('content');
        $call['checksum'] = $request->get('checksum');
        $call['checksum'] = md5($data['checksum']);
        $url = 'http://171.244.49.26:1028/CallOut';
        $myvars = json_encode($call);
        if (LichSuGoiDien::where('diem_chay_id', $data['diemchay_id'])->where('so_dien_thoai', $data['toNumber'])->count() >= 2) {
            return response()->json([
                'message' => 'Đã gọi điện 2 lần.',
            ], 422);
        }
        $callLog = LichSuGoiDien::where('toa_nha_id', $data['toa_nha_id'])->where('so_dien_thoai', $data['toNumber'])->latest()->first();
        if ($callLog != null && !Carbon::parse($callLog->updated_at)->addMinutes(2)->isPast()) {
            return response()->json([
                'message' => 'Đã thực hiện một cuộc gọi trước đó.',
            ], 422);
        };
        $lan_goi = LichSuGoiDien::where('toa_nha_id', $data['toa_nha_id'])->where('so_dien_thoai', $data['toNumber'])->count();
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
                    'toa_nha_id' => $data['toa_nha_id'],
                    'so_dien_thoai' => $data['toNumber'],
                    'diem_chay_id' => $data['diemchay_id']
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

    function call(Request $request)
    {
        $data = $request->all();
        $call['apiKey'] = '2982#djd838jdh!23';
        $call['toNumber'] = $request->get('toNumber');
        $call['content'] = $request->get('content');
        $call['checksum'] = $call['apiKey'] . $call['toNumber'] . $call['content'];
        $call['checksum'] = md5($call['checksum']);
        $url = 'http://171.244.49.26:1028/CallOut';
        $myvars = json_encode($call);
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể thực hiện cuộc gọi',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function baoChay(Request $request)
    {
         
        // $this->callServer("0866699806", "Chung cư Dương Nội  Tầng 24", 506, 77);
        
        $apiKey = $request->get('apiKey');
        $imeiThietBi = $request->get('imeiThietBi');
        if ($apiKey !== "abcd$1233@456") {
            return response()->json([
                'message' => 'Sai api key',
                'code' => 500,
            ], 500);
        }
        $imeiCamBien = $request->get('imeiCamBien');

        $thietBi = ThietBi::withoutGlobalScope(ToaNhaScope::class)->where('imei', $imeiThietBi)->first();
        if ($thietBi) {
            $toaNha = ToaNha::withoutGlobalScope(TinhThanhScope::class)->where('id', $thietBi->toa_nha_id)->first();
            if (!$toaNha) {
                return response()->json([
                    'message' => 'Tòa nhà không tồn tại',
                    'code' => 500,
                ], 500);
            }
            $camBien = CamBien::where('thiet_bi_id', $thietBi->id)->where('IMEI_thiet_bi', $imeiCamBien)->first();
            $phone = SoDienThoaiToaNha::where('toa_nha_id', $toaNha->id)->pluck('so_dien_thoai');
            $camBienID = null;
            $viTriCamBien =  $toaNha->ten ? "Tòa nhà ".$toaNha->ten : "";

            if ($camBien) {
                $camBienID = $camBien->id;
                $viTriCamBien = $viTriCamBien.", ".$camBien->vi_tri;
            }
            $checkDangChay = DiemChay::withoutGlobalScope(TinhThanhScope::class)->where('IMEI_thiet_bi', $imeiThietBi)->where('trang_thai', 'dang_chay')->first();
            if ($checkDangChay) {
                if(!$camBienID) return response()->json([
                    'message' => 'Điểm cháy đang tồn tại, imei cảm biến không hợp lệ',
                    'code' => 400,
                ], 400);

                if ($checkDangChay->cam_bien_id == $camBienID || $checkDangChay->cam_bien_tiep_theo == $camBienID) {
                    return response()->json([
                        'message' => 'Vị trí cháy đã tồn tại',
                        'code' => 402,
                    ], 500);
                } else {
                    DiemChay::where('id', $checkDangChay->id)->update(['cam_bien_tiep_theo' => $camBienID]);
                    foreach($phone as $it){
                        $this->callServer($it, $viTriCamBien,  $checkDangChay->id, $thietBi->toa_nha_id);
                    }
                    return response()->json([
                        'message' => 'Đã cập nhật cảm biến điểm cháy',
                        'code' => 200,
                    ], 200);
                }
            }
            $diemChay =  DiemChay::create([
                'ten' => $toaNha->ten,
                'so_dien_thoai' => null,
                'ten_nguoi_bao' => null,
                'toa_nha_id' => $thietBi->toa_nha_id,
                'lat' => $toaNha->lat,
                'long' => $toaNha->long,
                'dia_chi' => $toaNha->dia_chi,
                'trang_thai' => 'dang_chay',
                'thoi_gian_bao_chay' => Carbon::now(),
                'thoi_gian_bat_dau_xu_ly' => null,
                'thoi_gian_ket_thuc_xu_ly' => null,
                'mo_ta' => 'Cháy từ thiết bị',
                'nguyen_nhan' => null,
                'uoc_tinh_thiet_hai' => null,
                'so_nguoi_chet' => null,
                'so_nguoi_bi_thuong' => null,
                'tinh_thanh_id' => $toaNha->tinh_thanh_id,
                'cam_bien_id' => $camBienID,
                'so_nguoi_tham_gia_chua_chay' => null,
                'IMEI_thiet_bi' => $imeiThietBi,
                'cam_bien_tiep_theo' => null
            ]);
            foreach($phone as $it){
                $this->callServer($it, $viTriCamBien,  $diemChay->id, $thietBi->toa_nha_id);
            }
            return response()->json([
                'message' => 'Thành công! Đã tạo điểm cháy',
                'code' => 200,
            ], 200);
        } else
            return response()->json([
                'message' => 'IMEI Thiết bị không tồn tại',
                'code' => 200,
            ], 200);
    }

    
    function callServer($phoneNumber, $viTri, $diemChayID, $toaNhaID){

        if($phoneNumber[0] == 0){
            $phoneNumber = ltrim($phoneNumber, '0'); 
            $phoneNumber = '84'.$phoneNumber;
        }
        $content = "Cảnh báo, Cảnh báo ". $viTri." Đang xảy ra nguy cơ cháy. Các đơn vị kiểm tra xác minh. "." Cảnh báo, Cảnh báo ". $viTri." Đang xảy ra nguy cơ cháy. Các đơn vị kiểm tra xác minh";
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
}
