<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ToaNha;
use App\File;
use App\DiemChay;
use App\Http\Resources\ThietBiMobileResource;
use App\HuanLuyenBoiDuong;
use App\KiemTraToaNha;
use App\NhanSuThucTapPhuongAnChuaChay;
use App\PhuongTienThucTapPhuongAnChuaChay;
use App\PhuongTienToaNha;
use App\SoDienThoaiToaNha;
use App\ThamDinhPheDuyet;
use App\ThietBi;
use App\ThucTapPhuongAnChuaChay;
use App\ToaNhaThayDoiPccc;
use App\XuLyViPham;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Storage;

class BuildingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $toa_nha = ToaNha::where('id', $user->toa_nha_id)->first();

        return response()->json([
            'data' => ['building' => $toa_nha, 'user' => $user],
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function list(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = ToaNha::query()->with(['tinhThanh', 'quanHuyen', 'soDienThoai', 'donViPccc', 'loaiHinhSoHuu', 'thietBi']);
        $search = $request->get('search');
        $don_vi_pccc_id = $request->get('don_vi_pccc_id');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $date = $request->get('date');
        if (isset($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where('ma', 'ilike', "%{$search}%");
                $query->orWhere('ten', 'ilike', "%{$search}%");
                $query->orWhere('search', 'ilike', "%{$search}%");
            });
        }
        if (isset($date)) {
            $query->whereHas('thietBi', function ($query) use ($date) {
                $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                    ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
            });
        }
        if (isset($don_vi_pccc_id)) {
            $query->where('don_vi_pccc_id', $don_vi_pccc_id);
        }
        if (isset($tinh_thanh_id)) {
            $query->whereHas('tinhThanh', function ($query) use ($tinh_thanh_id) {
                $query->where('tinh_thanh_id', $tinh_thanh_id);
            });
        }
        $query->orderBy('updated_at', 'desc');
        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
        ], 200);
    }
    public function getToaNhaTheoTinhThanh(Request $request, $id)
    {
        $query = ToaNha::where('tinh_thanh_id', $id)->with('thietBi');
        $date = $request->get('date');
        if (isset($date)) {

            $query->whereHas('thietBi', function ($query) use ($date) {
                $query->where('created_at', '>=', Carbon::parse($date[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                    ->where('created_at', '<=', Carbon::parse($date[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
            });
        }
        $toa_nha = $query->get();
        return response()->json([
            'data' => $toa_nha,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }
    public function listALl()
    {
        $toa_nha = ToaNha::get();

        return response()->json([
            'data' => $toa_nha,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    public function show($id)
    {
        $donvi = ToaNha::with(['tinhThanh', 'quanHuyen', 'soDienThoai', 'donViPccc', 'loaiHinhSoHuu', 'files', 'thamDuyets.files', 'thietBi.loaiThietBi', 'kiemTraToaNhas.files', 'thayDoiPcccs', 'thayDoiPcccs.files', 'viPhams', 'huanLuyens', 'viPhams.nhomHanhVis', 'viPhams.files', 'vuChays', 'vuChays.camBienFirst', 'pcccCoSo', 'phuongTien'])->find($id);

        return response()->json([
            'data' => $donvi,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $info = $request->get('form');
        $dienthoais = $request->get('dien_thoais');
        $info['search'] = convert_vi_to_en($request->ten);
        if (isset($user->tinh_thanh_id)) {
            $info['tinh_thanh_id'] = $user->tinh_thanh_id;
        }
        $info['dien_thoai'] = 'xxx';
        $validator = Validator::make($info, [
            'ma' => 'required',
            'ten' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'dia_chi' => 'required',
            'tinh_thanh_id'  => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm tòa nhà'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {
            $info['ma'] = trim($info['ma']);
            if (ToaNha::where('ma', 'ilike', $info['ma'])->first()) {
                return response()->json([
                    'message' => "Mã toà nhà đã tồn tại",
                    'code' => 400,
                    'data' => ''
                ], 400);
            }
            $files = $info['fileList'];
            $toanha = ToaNha::create([
                "cap_quan_ly_hanh_chinh" => $info['cap_quan_ly_hanh_chinh'],
                "hinh_thuc_dau_tu" => $info['hinh_thuc_dau_tu'],
                "thanh_phan_kinh_te" => $info['thanh_phan_kinh_te'],
                "loai_hinh_hoat_dong" => $info['loai_hinh_hoat_dong'],
                "nganh_linh_vuc" => $info['nganh_linh_vuc'],
                "phai_mua_bhcnbb" => $info['phai_mua_bhcnbb'],
                "da_mua_bhcnbb" => $info['da_mua_bhcnbb'],
                "ma" => $info['ma'],
                "ten" => $info['ten'],
                "dien_thoai" => "xxx",
                "tinh_thanh_id" => $info['tinh_thanh_id'],
                "quan_huyen_id" => $info['quan_huyen_id'],
                "huong_vao_toa_nha" => $info['huong_vao_toa_nha'],
                "loai_hinh_so_huu_id" => $info['loai_hinh_so_huu_id'],
                "hien_thi_toa_nha" => $info['hien_thi_toa_nha'],
                "dia_chi" => $info['dia_chi'],
                "lat" => $info['lat'],
                "long" => $info['long'],
                "don_vi_pccc_id" => $info['don_vi_pccc_id'],
                "chu_toa_nha" => $info['chu_toa_nha'],
                "ngay_dang_ki_kd" => $info['ngay_dang_ki_kd'],
                "ngay_het_han_kd" => $info['ngay_het_han_kd'],
                "nam_thanh_lap" => $info['nam_thanh_lap'],
                "web" => $info['web'],
                "email" => $info['email'],
            ]);
            foreach ($files as $item) {
                if (!empty($item['response']['result'])) {
                    File::where('id', $item['response']['result'])->update(['reference_id' => $toanha->id]);
                }
            }
            foreach ($dienthoais as $phone) {
                SoDienThoaiToaNha::create([
                    'toa_nha_id' => $toanha->id,
                    'so_dien_thoai' => $phone
                ]);
            }
            return response()->json([
                'message' => 'Thành công',
                'data' => $toanha,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo tòa nhà',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $request->get('form');
        $dienthoais = $request->get('dien_thoais');
        $data['search'] = convert_vi_to_en($request->ten);
        $validator = Validator::make($data, [
            'ma' => 'required',
            'ten' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'tinh_thanh_id'  => 'required',
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
        try {
            $data['ma'] = trim($data['ma']);
            if (ToaNha::where('ma', 'ilike', $data['ma'])->where('id', '<>', $id)->first()) {
                return response(['message' => 'Mã tòa nhà đã tồn tại'], 400);
            }
            SoDienThoaiToaNha::where('toa_nha_id', $id)->delete();
            foreach ($dienthoais as $phone) {
                SoDienThoaiToaNha::create([
                    'toa_nha_id' => $id,
                    'so_dien_thoai' => $phone
                ]);
            }
            ToaNha::where('id', $id)->first()->update([
                "cap_quan_ly_hanh_chinh" => $data['cap_quan_ly_hanh_chinh'],
                "hinh_thuc_dau_tu" => $data['hinh_thuc_dau_tu'],
                "thanh_phan_kinh_te" => $data['thanh_phan_kinh_te'],
                "loai_hinh_hoat_dong" => $data['loai_hinh_hoat_dong'],
                "nganh_linh_vuc" => $data['nganh_linh_vuc'],
                "phai_mua_bhcnbb" => $data['phai_mua_bhcnbb'],
                "da_mua_bhcnbb" => $data['da_mua_bhcnbb'],
                "ma" => $data['ma'],
                "ten" => $data['ten'],
                "dien_thoai" => "xxx",
                "tinh_thanh_id" => $data['tinh_thanh_id'],
                "quan_huyen_id" => $data['quan_huyen_id'],
                "huong_vao_toa_nha" => $data['huong_vao_toa_nha'],
                "loai_hinh_so_huu_id" => $data['loai_hinh_so_huu_id'],
                "hien_thi_toa_nha" => $data['hien_thi_toa_nha'],
                "dia_chi" => $data['dia_chi'],
                "lat" => $data['lat'],
                "long" => $data['long'],
                "don_vi_pccc_id" => $data['don_vi_pccc_id'],
                "chu_toa_nha" => $data['chu_toa_nha'],
                "ngay_dang_ki_kd" => $data['ngay_dang_ki_kd'],
                "ngay_het_han_kd" => $data['ngay_het_han_kd'],
                "nam_thanh_lap" => $data['nam_thanh_lap'],
                "web" => $data['web'],
                "email" => $data['email'],
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
            ToaNha::find($id)->delete();

            return response()->json([
                'message' => 'Xóa thành công',
                'code' => 200,
                'data' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi, không thể xóa tòa nhà',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        $info = $request->all();
        $user = auth()->user();
        $validator = Validator::make($info, [
            'file' => 'required|file|max:32768',      // max 32MB = 32768KB,
        ]);

        if ($validator->fails()) {
            $message = 'validation failed';
            $failedRules = $validator->failed();

            if (isset($failedRules['file']['required'])) {
                $message = 'Tệp không được tìm thấy';
            } elseif (isset($failedRules['file']['file'])) {
                $message = 'Không hỗ trợ định dạng tệp';
            } elseif (isset($failedRules['file']['max'])) {
                $message = 'Kích thước tệp quá lớn';
            }

            return response()->json([
                'message' => $message,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $id = $request->get('id');
        $type_ref = $request->get('type_reference');

        $file_id = time();
        $fileUpload = $request->file;
        $fileName = $file_id . '-' . $fileUpload->getClientOriginalName();
        $fileUpload->storeAs('public/images/checkin', $fileName);
        $path = 'storage/images/checkin/' . $fileName;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data['url'] = url($path);
        $data['file_id'] = $file_id;
        $data['name'] = $fileName;
        if (empty($type_ref)) {
            $data['type'] = 'toa_nha';
        } else {
            $data['type'] = $type_ref;
        }

        $data['nguoi_tao'] = $user->id;
        if (!empty($id)) {
            $data['reference_id'] = $id;
        }
        $file = File::create($data);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'result' => $file->id,
        ]);
    }

    public function deleteFile($id)
    {
        try {
            $file = File::find($id);

            $fileName = 'public/images/checkin/' . $file->name;
            if (Storage::exists($fileName)) {
                Storage::delete($fileName);
            }
            $file->delete();

            return response()->json([
                'message' => 'Xóa thành công',
                'code' => 200,
                'data' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi, không thể xóa',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function getThietBi()
    {
        if (auth()->user()->toaNha && auth()->user()->role_id == 3) {

            $json = file_get_contents('http://171.244.49.26:2585/SAS/GetDeviceByUser/123456');
            $objs = collect(json_decode($json));
            $thietBis = auth()->user()->toaNha->thietBi()->with('loaiThietBi', 'user', 'camBien.loaiCamBien')->get();
            $thietBis->each(function ($thietBi) use ($objs) {

                $thietBi['online'] = false;
                $thietBi['loai'] = $thietBi->loaiThietBi->ten;
                $thietBi['ma_loai'] = $thietBi->loaiThietBi->ma;
                $thietBi['battery'] = null;
                if (isset($thietBi->user->name)) {
                    $thietBi['chu_so_huu'] = $thietBi->user->name;
                } else {
                    $thietBi['chu_so_huu'] = '';
                }


                $thietBi->camBien->each(function ($camBien) {
                    $camBien['online'] = false;
                    $camBien['dia_chi'] = $camBien->vi_tri;
                    $camBien['loai'] = $camBien->loaiCamBien->ten;
                    $camBien['ma_loai'] = $camBien->loaiCamBien->ma;
                    $camBien['battery'] = null;
                });

                $check = $objs->where('IMEI', $thietBi->imei)->first();
                if ($check) {
                    $thietBi['online'] = true;
                    $thietBi['battery'] = $check->Battery;
                    $camBiens = collect($check->Sensors);
                    $thietBi->camBien->each(function ($camBien) use ($camBiens) {
                        $checkCamBien = $camBiens->where('IMEI', $camBien->IMEI_thiet_bi)->first();
                        if ($checkCamBien) {
                            $camBien['online'] = true;
                            $camBien['battery'] = $checkCamBien->Battery;
                        }
                    });
                }
            });
            $result = collect([]);
            $thietBis->each(function ($thietBi) use (&$result) {
                $result->push($thietBi);
                $result = $result->merge($thietBi->camBien);
            });
            return  ThietBiMobileResource::collection($thietBis->merge($result));
        }
        return ['data' => []];
    }

    // public function getThietBi()
    // {
    //     if (auth()->user()->toaNha && auth()->user()->role_id == 3) {
    //        return ThietBi::where('toa_nha_id', auth()->user()->toa_nha_id)->with('camBien')->get();
    //     }
    //     return ['data' => []];
    // }

    public function getDownload(Request $request, $id)
    {
        $file = \App\File::query()
            ->where('id', $id)
            ->first();
        if (!empty($file)) {
            if (Storage::exists('public/images/checkin/' . $file->name)) {
                return response()->download(storage_path('app/public/images/checkin/' . $file->name));
            } else {
                return response()->json([
                    'message' => 'Không tìm thấy file',
                    'code' => 500,
                    'data' => [],
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Không tìm thấy dữ liệu',
                'code' => 500,
                'data' => [],
            ], 500);
        }
    }

    public function search()
    {
        $ma = ToaNha::select('ma')->pluck('ma')->all();
        $ten = ToaNha::select('ten')->pluck('ten')->all();
        $dia_chi = ToaNha::select('dia_chi')->pluck('dia_chi')->all();
        $data = array_merge($ma, $ten, $dia_chi);

        return $data;
    }

    public function addToaNha(Request $request, $id)
    {
        $user = auth()->user();
        $info = $request->all();
        $diemchay = DiemChay::where('id', $id)->first();
        $info['search'] = convert_vi_to_en($request->ten);
        if (isset($diemchay->tinh_thanh_id)) {
            $info['tinh_thanh_id'] = $diemchay->tinh_thanh_id;
        };
        if (isset($user->tinh_thanh_id)) {
            $info['tinh_thanh_id'] = $user->tinh_thanh_id;
        }
        $info['dien_thoai'] = 'xxx';
        $validator = Validator::make($info, [
            'ma' => 'required',
            'ten' => 'required',
            'dien_thoai' => 'required',
            'dia_chi' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => __('Không thể thêm tòa nhà'),
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        try {

            $info['lat'] = $diemchay->lat;
            $info['long'] = $diemchay->long;

            $toanha = ToaNha::create($info);
            return response()->json([
                'message' => 'Thành công',
                'data' => $toanha,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi ! Không thể tạo tòa nhà',
                'code' => 500,
                'data' => $e,
            ], 500);
        }
    }

    public function xuatWord($id)
    {
        $toaNha = ToaNha::where('id', $id)->with('tinhThanh', 'donViPccc', 'soDienThoai', 'loaiHinhSoHuu')->first();
        $soDienThoai = "";
        if ($toaNha->soDienThoai) {
            foreach ($toaNha->soDienThoai as $dt) {
                $soDienThoai = $dt->so_dien_thoai . ", " . $soDienThoai;
            }
        };
        $bhcn = "";
        if ($toaNha->phai_mua_bhcnbb && $toaNha->da_mua_bhcnbb) {
            $bhcn = "Cơ sở thuộc diện phải mua BHCNBB. Đã mua BHCNBB";
        }
        if ($toaNha->phai_mua_bhcnbb && !$toaNha->da_mua_bhcnbb) {
            $bhcn = "Cơ sở thuộc diện phải mua BHCNBB. Chưa mua BHCNBB";
        }
        if (!$toaNha->phai_mua_bhcnbb && $toaNha->da_mua_bhcnbb) {
            $bhcn = "Cơ sở không thuộc diện phải mua BHCNBB. Có mua BHCNBB";
        }
        if (!$toaNha->phai_mua_bhcnbb && !$toaNha->da_mua_bhcnbb) {
            $bhcn = "Cơ sở không thuộc diện phải mua BHCNBB. Chưa mua BHCNBB";
        }
        $template_file_name = public_path() . '/imports/TOANHA.docx';
        $fileName = "CTNH_01.docx";
        $folder   = "results_";
        $full_path = $folder . '/' . $fileName;
        try {
            if (!file_exists($folder)) {
                mkdir($folder);
            }
            //Copy the Template file to the Result Directory
            copy($template_file_name, $full_path);

            // add calss Zip Archive
            $zip_val = new \PhpOffice\PhpWord\Shared\ZipArchive();

            //Docx file is nothing but a zip file. Open this Zip File
            if ($zip_val->open($full_path) == true) {
                // In the Open XML Wordprocessing format content is stored.
                // In the document.xml file located in the word directory.

                $key_file_name = 'word/document.xml';
                $message = $zip_val->getFromName($key_file_name);
                $timestamp = date('d-M-Y H:i:s');
                // this data Replace the placeholders with actual values
                $message = str_replace("ten_toa_nha", $toaNha->ten,       $message);
                $message = str_replace("dia_chi",  $toaNha->dia_chi,  $message);
                $message = str_replace("tinh_thanh",  $toaNha->tinhThanh->name,  $message);
                $message = str_replace("chu_toa_nha",  $toaNha->chu_toa_nha,             $message);
                $message = str_replace("email_toanha",  $toaNha->email,  $message);
                $message = str_replace("web_toanha",  $toaNha->web,  $message);
                $message = str_replace("namthanhlap",  $toaNha->nam_thanh_lap,  $message);
                $message = str_replace("donvipcc",  $toaNha->donViPccc->ten,  $message);
                $message = str_replace("cap_quan_ly_hanh_chinh",  $toaNha->cap_quan_ly_hanh_chinh,  $message);
                $message = str_replace("hinh_thuc_dau_tu",  $toaNha->hinh_thuc_dau_tu,  $message);
                $message = str_replace("thanh_phan_kinh_te",  $toaNha->thanh_phan_kinh_te,  $message);
                $message = str_replace("loai_hinh_hoat_dong",  $toaNha->loai_hinh_hoat_dong,  $message);
                $message = str_replace("nganh_linh_vuc",  $toaNha->nganh_linh_vuc,  $message);
                $message = str_replace("so_dien_thoai",  $soDienThoai,  $message);
                $message = str_replace("loai_hinh",  $toaNha->loaiHinhSoHuu->ten,  $message);
                $message = str_replace("hv_toa_nha",  $toaNha->huong_vao_toa_nha, $message);
                $message = str_replace("bhcn",  $bhcn,  $message);

                $zip_val->addFromString($key_file_name, $message);
                $zip_val->close();  
                return response()->download($full_path);
            }
        } catch (Exception $exc) {
            $error_message =  "Error creating the Word Document";
            var_dump($exc);
        }
    }

    public function table($id)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $header = array('name' => 'Time new roman', 'size' => 13, 'bold' => true);
        $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $headerTable =  array('name' => 'Time new roman', 'size' => 11, 'bold' => true);
        $subTT =  array('name' => 'Time new roman', 'size' => 11, 'bold' => false);
        $toaNha = ToaNha::where('id', $id)->with('tinhThanh', 'donViPccc', 'soDienThoai', 'loaiHinhSoHuu')->first();
        $soDienThoai = "";
        if ($toaNha->soDienThoai) {
            foreach ($toaNha->soDienThoai as $dt) {
                $soDienThoai = $dt->so_dien_thoai . ", " . $soDienThoai;
            }
        };
        $bhcn = "";
        if ($toaNha->phai_mua_bhcnbb && $toaNha->da_mua_bhcnbb) {
            $bhcn = "Cơ sở thuộc diện phải mua BHCNBB. Đã mua BHCNBB";
        }
        if ($toaNha->phai_mua_bhcnbb && !$toaNha->da_mua_bhcnbb) {
            $bhcn = "Cơ sở thuộc diện phải mua BHCNBB. Chưa mua BHCNBB";
        }
        if (!$toaNha->phai_mua_bhcnbb && $toaNha->da_mua_bhcnbb) {
            $bhcn = "Cơ sở không thuộc diện phải mua BHCNBB. Có mua BHCNBB";
        }
        if (!$toaNha->phai_mua_bhcnbb && !$toaNha->da_mua_bhcnbb) {
            $bhcn = "Cơ sở không thuộc diện phải mua BHCNBB. Chưa mua BHCNBB";
        }

        $tieuDe = ['name' => 'Time new roman', 'bold' => true, 'align' => 'center', 'size' => 14];
        $section->addText('PHIẾU QUẢN LÝ CƠ SỞ VỀ PCCC', $tieuDe, [ 'align' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
        $section->addTextBreak(1);
        $section->addText('1. Cơ quan quản lý về PCCC: '.' '.$toaNha->donViPccc->ten, $headerTable);
        $section->addText('2. Tỉnh thành: '.' '. $toaNha->tinhThanh->name, $headerTable);
        $section->addText('3. Tên cơ sở: '.' '. $toaNha->ten, $headerTable);
        $section->addText('Email: '.' '. $toaNha->email, $subTT);
        $section->addText('Website: '.' '. $toaNha->web, $subTT);
        $section->addText('Hướng vào tòa nhà: '.' '. $toaNha->huong_vao_toa_nha, $subTT);
        $section->addText('4. Năm thành lập: '.' '. $toaNha->nam_thanh_lap, $headerTable);
        $section->addText('5. Số điện thoại: '.' '. $soDienThoai, $headerTable);
        $section->addText('6. Địa chỉ: '.' '. $toaNha->dia_chi, $headerTable);
        $section->addText('7. Loại hình sở hữu: '.' '.$toaNha->loaiHinhSoHuu->ten, $headerTable);
        $section->addText('8. Người đứng đầu: '.' '.$toaNha->chu_toa_nha, $headerTable);
        $section->addText('9. Cấp quản lý hành chính: '.' '.$toaNha->cap_quan_ly_hanh_chinh, $headerTable);
        $section->addText('10. Hình thức đầu tư: '.' '.$toaNha->hinh_thuc_dau_tu, $headerTable);
        $section->addText('11. Thuộc thành phần kinh tế: '.' '.$toaNha->thanh_phan_kinh_te, $headerTable);
        $section->addText('12. Cơ sở thuộc hệ:', $headerTable);
        $section->addText('Loại hình hoạt động: '.' '.$toaNha->loai_hinh_hoat_dong, $subTT);
        $section->addText('Thuộc ngành lĩnh vực: '.' '.$toaNha->nganh_linh_vuc, $subTT);
        $section->addText('13. Việc thực hiện quy định về mua bảo hiểm cháy nổ bắt buộc:  ', $headerTable);
        $section->addText($bhcn,$subTT);

        $section->addPageBreak();
        $data = ThamDinhPheDuyet::where('toa_nha_id', $id)->get();
        $section->addText('Thẩm duyệt cấp giấy chứng nhận PCCC', $header);

        $table = $section->addTable([
            'borderColor' => 'black',
            'borderSize'  => 10
        ]);

        $table->addRow();
        $table->addCell(1500)->addText('STT', $headerTable, $cellHCentered);
        $table->addCell(5000)->addText('Số văn bản', $headerTable, $cellHCentered);
        $table->addCell(5000)->addText('Ngày cấp', $headerTable, $cellHCentered);
        $table->addCell(5000)->addText('Cơ quan cấp', $headerTable, $cellHCentered);
        $table->addCell(5000)->addText('Ghi chú', $headerTable, $cellHCentered);
        $stt = 0;
        foreach ($data as $it) {
            $stt++;
            $table->addRow();
            $table->addCell(1000)->addText($stt);
            $table->addCell(5000)->addText($it->so_van_ban);
            $table->addCell(5000)->addText(Carbon::parse($it->ngay_cap)->format('d/m/Y'));
            $table->addCell(5000)->addText($it->so_van_ban);
            $table->addCell(5000)->addText($it->ghi_chu);
        }

        $section->addTextBreak(2);
        $phuongTien = PhuongTienToaNha::where('toa_nha_id', $id)->get();
        $section->addText('Phương tiện PCCC và CNCH', $header);
        $table = $section->addTable([
            'borderColor' => 'black',
            'borderSize'  => 10
        ]);

        $table->addRow();
        $table->addCell(1500)->addText('STT',$headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Tên phương tiện', $headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Loại phương tiện', $headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Phân loại chi tiết', $headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Số lượng', $headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Tình trạng hoạt động', $headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Vị trí bố trí, lắp đặt', $headerTable, $cellHCentered);
        $stt = 0;
        foreach ($phuongTien as $it) {
            $stt++;
            $table->addRow();
            $table->addCell(1000)->addText($stt);
            $table->addCell(4000)->addText($it->ten);
            $table->addCell(4000)->addText($it->loai);
            $table->addCell(4000)->addText($it->loai_chi_tiet);
            $table->addCell(4000)->addText($it->so_luong);
            $table->addCell(4000)->addText($it->tinh_trang);
            $table->addCell(4000)->addText($it->vi_tri);
        }

        $section->addTextBreak(2);
        $section->addText('Tuyên truyền huấn luyện, và bồi dưỡng nghiệp vụ PCCC', $header);
        $huanLuyen = HuanLuyenBoiDuong::where('toa_nha_id', $id)->get();

        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center', 'bgColor' => 'white');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 4, 'valign' => 'center');
        $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $cellVCentered = array('valign' => 'center');
        $table = $section->addTable([
            'borderColor' => 'black',
            'borderSize'  => 10
        ]);
        $table->addRow();
        $table->addCell(1000, $cellRowSpan)->addText('STT', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellRowSpan)->addText('Thời gian', $headerTable, $cellHCentered);
        $table->addCell(4000, $cellRowSpan)->addText('Nội dung huấn luyện bồi dưỡng nghiệp vụ PCCC', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellRowSpan)->addText('Thời lượng', $headerTable, $cellHCentered);
        $table->addCell(8000, $cellColSpan)->addText('Số lượng đối tượng tham gia', $headerTable, $cellHCentered);
        $table->addCell(3000, $cellRowSpan)->addText('Số giấy CN HLNV PCCC được cấp', $headerTable, $cellHCentered);

        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(2000, $cellVCentered)->addText('Lực lượng PCCC cơ sở', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Cán bộ quản lý, lãnh đạo', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Người lao động', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Đối tượng khác', $headerTable, $cellHCentered);
        $table->addCell(null, $cellRowContinue);

        $stt = 0;
        foreach ($huanLuyen as $it) {
            $stt++;
            $table->addRow();
            $table->addCell(1000)->addText($stt);
            $table->addCell(2000)->addText(Carbon::parse($it->thoi_gian)->format('d/m/Y'));
            $table->addCell(4000)->addText($it->noi_dung);
            $table->addCell(2000)->addText($it->thoi_luong . " giờ");
            $table->addCell(2000)->addText($it->pccc_co_so . " người");
            $table->addCell(2000)->addText($it->quan_ly_lanh_dao . " người");
            $table->addCell(2000)->addText($it->nguoi_lao_dong . " người");
            $table->addCell(2000)->addText($it->doi_tuong_khac . " người");
            $table->addCell(2000)->addText($it->so_giay_cn);
        }
        $section->addTextBreak(2);
        $kiemtra = KiemTraToaNha::where('toa_nha_id', $id)->get();
        $section->addText('Công tác kiểm tra tòa nhà', $header);
        $table = $section->addTable([
            'borderColor' => 'black',
            'borderSize'  => 8
        ]);
        $table->addRow();
        $table->addCell(1000)->addText('STT', $headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Thời gian kiểm tra', $headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Cán bộ kiểm tra', $headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Chế độ kiểm tra', $headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Những tồn tại thiếu sót về an toàn PCCC',$headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Nội dung yêu cầu, kiến nghị',$headerTable, $cellHCentered);
        $stt = 0;
        foreach ($kiemtra as $it) {
            $stt++;
            $table->addRow();
            $table->addCell(1000)->addText($stt);
            $table->addCell(4000)->addText(Carbon::parse($it->ngay_kiem_tra)->format('d/m/Y'));
            $table->addCell(4000)->addText($it->can_bo_kiem_tra);
            $table->addCell(4000)->addText($it->thong_tin);
            $table->addCell(4000)->addText($it->mo_ta);
            $table->addCell(4000)->addText($it->danh_gia);
        }
        $section->addTextBreak(2);
        $section->addText('Công tác xử lý vi phạm PCCC', $header);
        $viPham = XuLyViPham::query()->with('nhomHanhVis')->where('toa_nha_id', $id)->get();

        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center', 'bgColor' => 'white');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan1 = array('gridSpan' => 2, 'valign' => 'center');
        $cellColSpan2 = array('gridSpan' => 6, 'valign' => 'center');
        $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $cellVCentered = array('valign' => 'center');

        $table = $section->addTable([
            'borderColor' => 'black',
            'borderSize'  => 10
        ]);
        $table->addRow();
        $table->addCell(1000, $cellRowSpan)->addText('STT', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellRowSpan)->addText('Thời gian', $headerTable, $cellHCentered);
        $table->addCell(8000, $cellColSpan1)->addText('Hành vi vi Phạm PCCC', $headerTable, $cellHCentered);
        $table->addCell(3000, $cellRowSpan)->addText('Đối tượng vi phạm', $headerTable, $cellHCentered);
        $table->addCell(12000, $cellColSpan2)->addText('Biện pháp xử lý', $headerTable, $cellHCentered);

        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(3000, $cellVCentered)->addText('Nội dung hành vi cụ thể', $headerTable, $cellHCentered);
        $table->addCell(5000, $cellVCentered)->addText('Thuộc nhóm hành vi', $headerTable, $cellHCentered);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(2000, $cellVCentered)->addText('Cảnh cáo', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Phạt tiền (Tr. đồng)', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Ngày tạm đình chỉ', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Ngày phục hồi', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Đình chỉ', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Biện pháp xử lý khác', $headerTable, $cellHCentered);

        $stt = 0;
        foreach ($viPham as $it) {
            $stt++;
            $table->addRow();
            $table->addCell(1000)->addText($stt);
            $table->addCell(2000)->addText(Carbon::parse($it->thoi_gian)->format('d/m/Y'));
            $table->addCell(3000)->addText($it->noi_dung_hanh_vi);
            $nhomHanhVi = "";
            if ($it->nhomHanhVis) {
                foreach ($it->nhomHanhVis as $nhom) {
                    $nhomHanhVi = $nhom->ten_nhom_hanh_vi . "; " . $nhomHanhVi;
                }
            }
            $table->addCell(5000)->addText($nhomHanhVi);
            $table->addCell(3000)->addText($it->doi_tuong_vi_pham);

            $canhCao = "";
            $phatTien = "";
            $ngayTamDinhChi = "";
            $ngayPhucHoi = "";
            $dinhChi = "";
            if ($it->canh_cao) {
                $canhCao = "Đã cảnh cáo";
            }
            if ($it->phat_tien) {
                $phatTien = $it->phat_tien;
            }
            if ($it->tam_dinh_chi && $it->ngay_tam_dinh_chi && $it->ngay_phuc_hoi) {
                $ngayTamDinhChi = Carbon::parse($it->ngay_tam_dinh_chi)->format('d/m/Y');
                $ngayPhucHoi = Carbon::parse($it->ngay_phuc_hoi)->format('d/m/Y');
            }
            if ($it->dinh_chi) {
                $dinhChi = "Đã đình chỉ";
            }
            $table->addCell(2000)->addText($canhCao);
            $table->addCell(2000)->addText($phatTien);
            $table->addCell(2000)->addText($ngayTamDinhChi);
            $table->addCell(2000)->addText($ngayPhucHoi);
            $table->addCell(2000)->addText($dinhChi);
            $table->addCell(2000)->addText($it->xu_ly_khac);
        }

        $section->addTextBreak(2);
        $section->addText('Tình hình cháy nổ', $header);
        $diemchay = DiemChay::where('toa_nha_id', $id)->get();

        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center', 'bgColor' => 'white');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 3, 'valign' => 'center');
        $cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center');
        $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $cellVCentered = array('valign' => 'center');

        $table = $section->addTable([
            'borderColor' => 'black',
            'borderSize'  => 10
        ]);
        $table->addRow();
        $table->addCell(1000, $cellRowSpan)->addText('STT', $headerTable, $cellHCentered);
        $table->addCell(2000, $cellRowSpan)->addText('Thời gian xảy ra cháy', $headerTable, $cellHCentered);
        $table->addCell(4000, $cellRowSpan)->addText('Nơi phát sinh cháy nổ', $headerTable, $cellHCentered);
        $table->addCell(4000, $cellRowSpan)->addText('Nguyên nhân cháy nổ', $headerTable, $cellHCentered);
        $table->addCell(9000, $cellColSpan)->addText('Thiệt hại', $headerTable, $cellHCentered);
        $table->addCell(6000, $cellColSpan2)->addText('Công tác xử lý sau cháy', $headerTable, $cellHCentered);

        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);
        $table->addCell(3000, $cellVCentered)->addText('Số người chết', $headerTable, $cellHCentered);
        $table->addCell(3000, $cellVCentered)->addText('Số người bị thương', $headerTable, $cellHCentered);
        $table->addCell(3000, $cellVCentered)->addText('Tài sản (Tr. đồng)', $headerTable, $cellHCentered);
        $table->addCell(3000, $cellVCentered)->addText('Xử lý hành chính', $headerTable, $cellHCentered);
        $table->addCell(3000, $cellVCentered)->addText('Khởi tố vụ án', $headerTable, $cellHCentered);
        $stt = 0;

        foreach ($diemchay as $it) {
            $stt++;
            $table->addRow();
            $table->addCell(1000)->addText($stt);
            $table->addCell(2000)->addText(Carbon::parse($it->thoi_gian_bao_chay)->format('d/m/Y H:i:s'));
            $table->addCell(2000)->addText($it->dia_chi);
            $table->addCell(2000)->addText($it->nguyen_nhan);
            $table->addCell(2000)->addText($it->so_nguoi_chet . " người");
            $table->addCell(2000)->addText($it->so_nguoi_bi_thuong . " người");
            $table->addCell(2000)->addText($it->uoc_tinh_thiet_hai);
            $table->addCell(2000)->addText('');
            $table->addCell(2000)->addText('');
        }

        $thaydoi = ToaNhaThayDoiPccc::where('toa_nha_id', $id)->get();
        $section->addTextBreak(2);
        $section->addText('Những thay đổi của cơ sở có liên quan đến công tác PCCC', $header);
        $table = $section->addTable([
            'borderColor' => 'black',
            'borderSize'  => 10
        ]);

        $table->addRow();
        $table->addCell(1000)->addText('STT',$headerTable, $cellHCentered);
        $table->addCell(4000)->addText('Thời gian',$headerTable, $cellHCentered);
        $table->addCell(12000)->addText('Nội dung thay đổi',$headerTable, $cellHCentered);
        $table->addCell(6000)->addText('Ghi chú',$headerTable, $cellHCentered);
        $stt = 0;
        foreach ($thaydoi as $it) {
            $stt++;
            $table->addRow();
            $table->addCell(1000)->addText($stt);
            $table->addCell(2500)->addText(Carbon::parse($it->thoi_gian)->format('d/m/Y'));
            $table->addCell(2500)->addText($it->noi_dung);
            $table->addCell(2500)->addText($it->ghi_chu);
        }

        $obj = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $obj->save(storage_path('toanha.docx'));
        } catch (\Exception $e) {
        }
        return response()->download(storage_path('toanha.docx'));
    }
}
