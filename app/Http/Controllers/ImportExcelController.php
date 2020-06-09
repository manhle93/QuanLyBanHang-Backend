<?php

namespace App\Http\Controllers;

use App\CanBoChienSi;
use App\CapBac;
use App\ChucVu;
use App\DanhMuc;
use App\DiemLayNuoc;
use App\DonViHoTro;
use App\DonViPccc;
use App\PhuongTienPccc;
use App\QuanHuyen;
use App\SoDienThoaiToaNha;
use App\ThietBi;
use App\TinhThanh;
use App\ToaNha;
use Illuminate\Http\Request;
use App\Traits\ExecuteExcel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;
use Symfony\Component\HttpFoundation\Response;

use App\ChuyenToaDo\proj4php\proj4php;
use App\ChuyenToaDo\proj4php\Proj4phpProj;
use App\ChuyenToaDo\proj4php\proj4phpPoint;


class ImportExcelController extends Controller
{
    use ExecuteExcel;
    function downloadHdsd()
    {
        $excelFile = public_path() . '/imports/HDSD.pdf';
        return response()->download($excelFile);
    }

    function downloadMauThietBi(Request $request)
    {
        $tinh_thanh_id = $request->tinh_thanh_id;
        $query = ToaNha::with('tinhThanh');
        $loaiThietBi = DanhMuc::where('parent_id', 3)->get();
        if (isset($tinh_thanh_id)) {
            $query->where('tinh_thanh_id', $tinh_thanh_id);
        }
        $toaNha = $query->get();
        try {
            $excelFile = public_path() . '/imports/thietbi.xlsx';
            $this->load($excelFile, 'Data', function ($sheet) use ($toaNha, $loaiThietBi) {
                $cell = 5;
                $cell2 = 5;
                foreach ($loaiThietBi as $item) {
                    $sheet->setCellValue('B' . $cell, $item->ma);
                    $sheet->setCellValue('C' . $cell, $item->ten);
                    $cell++;
                }
                foreach ($toaNha as $el) {
                    $sheet->setCellValue('F' . $cell2, $el->ma);
                    $sheet->setCellValue('G' . $cell2, $el->ten);
                    $sheet->setCellValue('H' . $cell2, $el->tinhThanh->name);
                    $cell2++;
                }
            })->download('xlsx');
        } catch (\Exception $e) {
            dd($e);
        }
    }


    public function importThietBi(Request $request)
    {
        $file = $request->file('file');
        if (empty($file)) {
            return response()->json([
                'data' => [],
                'message' => 'Không tìm thấy file',
                'code' => 404,
            ], 404);
        }
        $file_id = time();
        $fileName = $file_id . '_' . $file->getClientOriginalName();
        $file->storeAs('public/imports/', $fileName);
        global $done;
        $done = 0;
        if (Storage::exists('public/imports/' . $fileName)) {
            DB::beginTransaction();
            try {
                \Excel::filter('chunk')->load(storage_path('app/public/imports/' . $fileName))->chunk(200, function ($reader) {
                    global $done;
                    foreach ($reader[0] as $row) {
                        $user = auth()->user();
                        $info = $row->all();
                        $thietBi['ma'] = trim($info['ma']);
                        $thietBi['ten'] = trim($info['ten_thiet_bi']);
                        $thietBi['imei'] = trim($info['imei']);
                        $thietBi['mo_ta'] = trim($info['mo_ta']);
                        $thietBi['dia_chi'] = trim($info['dia_chi']);
                        if (isset($user->tinh_thanh_id)) {
                            $thietBi['toa_nha_id'] = ToaNha::where('tinh_thanh_id', $user->tinh_thanh_id)->where('ma', 'ilike', trim($info['ma_toa_nha']))->first()->id;
                        } else {
                            $thietBi['toa_nha_id'] = ToaNha::where('ma', 'ilike', trim($info['ma_toa_nha']))->first()->id;
                        }

                        $thietBi['loai_thiet_bi_id'] = DanhMuc::where('parent_id', 3)->where('ma', 'ilike', trim($info['ma_loai_thiet_bi']))->first()->id;
                        if (ThietBi::where('imei', 'ilike', $thietBi['imei'])->first()) {
                            $done = 1;
                            DB::rollback();
                            break;
                        }
                        if (ThietBi::where('ma', 'ilike', $thietBi['ma'])->first()) {
                            $done = 2;
                            DB::rollback();
                            break;
                        }
                        $emp = ThietBi::create($thietBi);
                        $emp->save();
                    };
                    DB::commit();
                });
            } catch (\Exception $exception) {
                DB::rollback();
                return response()->json([
                    'data' => $exception,
                    'message' => 'Không thể upload, Vui lòng kiểm tra lại dữ liệu nhập',
                    'code' => 500,
                ], 500);
            }
        }
        if ($done == 0) {
            return response(['message' => 'created'], Response::HTTP_CREATED);
        }
        if ($done == 1) {
            return response(['message' => 'Không thể upload, IMEI bị trùng'], 500);
        }
        if ($done == 2) {
            return response(['message' => 'Không thể upload, Mã thiết bị đã tồn tại'], 500);
        }
    }

    function downloadMauToaNha(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        try {
            if (isset($tinh_thanh_id)) {
                $excelFile = public_path() . '/imports/toanha_qltinhthanh.xlsx';
            } else
                $excelFile = public_path() . '/imports/toanha_admin.xlsx';
            $this->load($excelFile, 'Sheet1', function ($sheet) {
            })->download('xlsx');
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function importToaNha(Request $request)
    {
        $file = $request->file('file');
        if (empty($file)) {
            return response()->json([
                'data' => [],
                'message' => 'Không tìm thấy file',
                'code' => 404,
            ], 404);
        }
        $file_id = time();
        $fileName = $file_id . '_' . $file->getClientOriginalName();
        $file->storeAs('public/imports/', $fileName);
        global $done;
        $done = 0;
        if (Storage::exists('public/imports/' . $fileName)) {
            DB::beginTransaction();
            try {
                \Excel::filter('chunk')->load(storage_path('app/public/imports/' . $fileName))->chunk(200, function ($reader) {
                    global $done;
                    foreach ($reader as $row) {
                        $user = auth()->user();
                        $info = $row->all();
                        $toaNha['ma'] = trim($info['ma_toa_nha']);
                        $toaNha['ten'] = trim($info['ten_toa_nha']);
                        $toaNha['huong_vao_toa_nha'] = trim($info['huong_vao_toa_nha']);
                        $toaNha['dien_thoai'] = trim($info['so_dien_thoai']);
                        $toaNha['dia_chi'] = trim($info['dia_chi']);
                        $toaNha['chu_toa_nha'] = trim($info['chu_toa_nha']);
                        $toaNha['long'] = trim($info['kinh_do']);
                        $toaNha['lat'] = trim($info['vi_do']);

                        if (isset($user->tinh_thanh_id) && $user->role_id == 2) {
                            $toaNha['tinh_thanh_id'] =  $user->tinh_thanh_id;
                        } else {
                            $toaNha['tinh_thanh_id'] = trim($info['tinh_thanh']);
                            if ($toaNha['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()) {
                                $toaNha['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()->id;
                            } else {
                                DB::rollback();
                                $done = 1;
                                break;
                            }
                        }

                        if (ToaNha::where('ma', $toaNha['ma'])->first()) {
                            DB::rollback();
                            $done = 2;
                            break;
                        }
                        if(!$toaNha['dien_thoai'] || $toaNha['dien_thoai'] == ""){
                            DB::rollback();
                            $done = 3;
                            break;
                        }
                        $emp = ToaNha::create($toaNha);
                        $emp->save();
                        SoDienThoaiToaNha::create([
                            'toa_nha_id' => $emp->id,
                            'so_dien_thoai' => $toaNha['dien_thoai']
                        ]);
                    };
                    DB::commit();
                });
            } catch (\Exception $exception) {
                DB::rollback();
                return response()->json([
                    'data' => $exception,
                    'message' => 'Không thể upload, Vui lòng kiểm tra lại dữ liệu nhập',
                    'code' => 500,
                ], 500);
            }
        }
        if ($done == 0) {
            return response(['message' => 'created'], Response::HTTP_CREATED);
        }
        if ($done == 1) {
            return response()->json([
                'message' => 'Tỉnh thành không hợp lệ! Không thể nhập dữ liệu',
                'code' => 500,
            ], 500);
        }
        if ($done == 2) {
            return response()->json([
                'message' => 'Mã tòa nhà đã tồn tại! Không thể nhập dữ liệu',
                'code' => 500,
            ], 500);
        }
        if ($done == 3) {
            return response()->json([
                'message' => 'Số điện thoại không được bỏ trống! Không thể nhập dữ liệu',
                'code' => 500,
            ], 500);
        }
    }

    function downloadMauNhanSu(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        try {
            if (isset($tinh_thanh_id)) {
                $excelFile = public_path() . '/imports/canbochiensi_tinhthanh.xlsx';
            } else
                $excelFile = public_path() . '/imports/canbochiensi.xlsx';
            $this->load($excelFile, 'Sheet1', function ($sheet) {
            })->download('xlsx');
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function importNhanSu(Request $request)
    {
        $file = $request->file('file');
        if (empty($file)) {
            return response()->json([
                'data' => [],
                'message' => 'Không tìm thấy file',
                'code' => 404,
            ], 404);
        }
        $file_id = time();
        $fileName = $file_id . '_' . $file->getClientOriginalName();
        $file->storeAs('public/imports/', $fileName);
        global $done;
        $done = 0;
        if (Storage::exists('public/imports/' . $fileName)) {
            DB::beginTransaction();
            try {
                \Excel::filter('chunk')->load(storage_path('app/public/imports/' . $fileName))->chunk(200, function ($reader) {
                    global $done;
                    foreach ($reader as $row) {
                        $user = auth()->user();
                        $info = $row->all();
                        $nhanSu['ten'] = trim($info['ho_ten']);
                        $nhanSu['cmnd'] = trim($info['so_cmt']);
                        $nhanSu['so_dien_thoai'] = trim($info['so_dien_thoai']);

                        if (isset($user->tinh_thanh_id) && $user->role_id == 2) {
                            $nhanSu['tinh_thanh_id'] =  $user->tinh_thanh_id;
                        } else {
                            if ($nhanSu['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()) {
                                $nhanSu['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()->id;
                            } else {
                                $done = 1;
                                DB::rollback();
                                break;
                            }
                        }

                        if (mb_strtolower($info['truc_thuoc']) != 'pc07' && strtolower($info['truc_thuoc']) != 'quận huyện') {
                            DB::rollback();
                            $done = 2;
                            break;
                        }
                        if (CapBac::where('ten_cap_bac', 'ilike', $info['cap_bac'])->first()) {
                            $nhanSu['cap_bac_id'] = CapBac::where('ten_cap_bac', 'ilike', $info['cap_bac'])->first()->id;
                        } else {
                            DB::rollback();
                            $done = 3;
                            break;
                        }
                        if (ChucVu::where('ten', 'ilike', $info['chuc_vu'])->first()) {
                            $nhanSu['chuc_vu_id'] = ChucVu::where('ten', 'ilike', $info['chuc_vu'])->first()->id;
                        } else {
                            DB::rollback();
                            $done = 4;
                            break;
                        }
                        if (mb_strtolower($info['truc_thuoc']) == 'pc07') {
                            $nhanSu['quan_huyen_id'] = null;
                            $nhanSu['loai_nhan_su'] = null;
                            if (DonViPccc::where('tinh_thanh_id', $nhanSu['tinh_thanh_id'])->where('ten', 'ilike', $info['don_vi_pccc'])->first()) {
                                $nhanSu['don_vi_pccc_id'] = DonViPccc::where('tinh_thanh_id', $nhanSu['tinh_thanh_id'])->where('ten', 'ilike', $info['don_vi_pccc'])->first()->id;
                            } else {
                                DB::rollback();
                                $done = 5;
                                break;
                            }
                        }
                        if (mb_strtolower($info['truc_thuoc']) == 'quận huyện') {
                            $nhanSu['don_vi_pccc_id'] = null;
                            if (QuanHuyen::where('tinh_thanh_id', $nhanSu['tinh_thanh_id'])->where('name', 'ilike', $info['ten_quan_huyen'])->first()) {
                                $nhanSu['quan_huyen_id'] = QuanHuyen::where('tinh_thanh_id', $nhanSu['tinh_thanh_id'])->where('name', 'ilike', $info['ten_quan_huyen'])->first()->id;
                            } else {
                                DB::rollback();
                                $done = 7;
                                break;
                            }
                            if (mb_strtolower($info['phong']) != 'phòng cháy' && mb_strtolower($info['phong']) != 'công tác cc&cnch') {
                                DB::rollback();
                                $done = 6;
                                break;
                            }
                            if (mb_strtolower($info['phong']) == 'phòng cháy') {
                                $nhanSu['loai_nhan_su'] = 'phong_ngua';
                            }
                            if (mb_strtolower($info['phong']) == 'công tác cc&cnch') {
                                $nhanSu['loai_nhan_su'] = 'cnch';
                            }
                        }
                        if(CanBoChienSi::where('cmnd', trim($info['so_cmt']))->first()){
                            DB::rollback();
                            $done = 8;
                            break;
                        }

                        $emp = CanBoChienSi::create($nhanSu);
                        $emp->save();
                    };
                    DB::commit();
                    return response(['message' => 'created'], Response::HTTP_CREATED);
                });
            } catch (\Exception $exception) {
                DB::rollback();
                return response()->json([
                    'data' => $exception,
                    'message' => 'Không thể upload, Vui lòng kiểm tra lại dữ liệu nhập',
                    'code' => 500,
                ], 500);
            }
        }
        if ($done == 0) {
            return response(['message' => 'created'], Response::HTTP_CREATED);
        }
        if ($done == 1) {
            return response(['message' => "Tỉnh thành không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 2) {
            return response(['message' => "Dữ liệu trường trực thuộc không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 3) {
            return response(['message' => "Cấp bậc không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 4) {
            return response(['message' => "Chức vụ không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 5) {
            return response(['message' => "Tên đơn vị PCCC không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 6) {
            return response(['message' => "Tên phòng không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 7) {
            return response(['message' => "Tên quận huyện không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 8) {
            return response(['message' => "Số chứng minh thư bị trùng! Không thể nhập dữ liệu"], 500);
        }
    }

    function downloadMauPhuongTien(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        try {
            if (isset($tinh_thanh_id)) {
                $excelFile = public_path() . '/imports/phuongtien_tinhthanh.xlsx';
            } else
                $excelFile = public_path() . '/imports/phuongtien.xlsx';
            $this->load($excelFile, 'Sheet1', function ($sheet) {
            })->download('xlsx');
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function importPhuongTien(Request $request)
    {
        $file = $request->file('file');
        if (empty($file)) {
            return response()->json([
                'data' => [],
                'message' => 'Không tìm thấy file',
                'code' => 404,
            ], 404);
        }
        $file_id = time();
        $fileName = $file_id . '_' . $file->getClientOriginalName();
        $file->storeAs('public/imports/', $fileName);
        global $done;
        $done = 0;
        if (Storage::exists('public/imports/' . $fileName)) {
            DB::beginTransaction();
            try {
                \Excel::filter('chunk')->load(storage_path('app/public/imports/' . $fileName))->chunk(200, function ($reader) {
                    global $done;
                    foreach ($reader as $row) {
                        $user = auth()->user();
                        $info = $row->all();
                        $phuongTien['ten'] = trim($info['ten_phuong_tien']);
                        $phuongTien['bien_so'] = trim($info['bien_so']);
                        $phuongTien['so_dien_thoai'] = trim($info['so_dien_thoai']);
                        $phuongTien['so_hieu'] = trim($info['so_hieu']);
                        $phuongTien['imei'] = trim($info['imei_thiet_bi']);
                        $phuongTien['trang_thai_hoat_dong'] = 'Tốt';

                        if (isset($user->tinh_thanh_id) && $user->role_id == 2) {
                            $phuongTien['tinh_thanh_id'] =  $user->tinh_thanh_id;
                        } else {
                            if ($phuongTien['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()) {
                                $phuongTien['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()->id;
                            } else {
                                DB::rollback();
                                $done = 1;
                                break;
                            }
                        }
                        if (isset($info['loai_phuong_tien'])) {
                            $info['loai_phuong_tien'] = trim($info['loai_phuong_tien']);
                            if (DanhMuc::where('parent_id', 6)->where('ten', 'ilike', $info['loai_phuong_tien'])->first()) {
                                $phuongTien['loai_phuong_tien_pccc_id'] = DanhMuc::where('parent_id', 6)->where('ten', 'ilike', $info['loai_phuong_tien'])->first()->id;
                            } else {
                                $phuongTien['loai_phuong_tien_pccc_id'] = null;
                            }
                        }
                        if (mb_strtolower($info['truc_thuoc']) != 'pc07' && strtolower($info['truc_thuoc']) != 'quận huyện') {
                            DB::rollback();
                            $done = 2;
                            break;
                        }

                        if (mb_strtolower($info['truc_thuoc']) == 'pc07') {
                            $phuongTien['quan_huyen_id'] = null;
                            $phuongTien['don_vi_pccc_quan_ly'] = true;
                            if (!DonViPccc::where('tinh_thanh_id', $phuongTien['tinh_thanh_id'])->where('ten', 'ilike', $info['don_vi_pccc'])->first()) {
                                DB::rollback();
                                $done = 3;
                                break;
                            }
                            $phuongTien['don_vi_pccc_id'] = DonViPccc::where('tinh_thanh_id', $phuongTien['tinh_thanh_id'])->where('ten', 'ilike', $info['don_vi_pccc'])->first()->id;
                        }
                        if (mb_strtolower($info['truc_thuoc']) == 'quận huyện') {
                            $phuongTien['don_vi_pccc_id'] = null;
                            if (!QuanHuyen::where('tinh_thanh_id', $phuongTien['tinh_thanh_id'])->where('name', 'ilike', $info['ten_quan_huyen'])->first()) {
                                DB::rollback();
                                $done = 4;
                                break;
                            }
                            $phuongTien['quan_huyen_id'] = QuanHuyen::where('tinh_thanh_id', $phuongTien['tinh_thanh_id'])->where('name', 'ilike', $info['ten_quan_huyen'])->first()->id;
                        }
                        if(PhuongTienPccc::where('imei', $phuongTien['imei'])->first()){
                            DB::rollback();
                            $done = 5;
                            break;
                        }
                        $emp = PhuongTienPccc::create($phuongTien);
                        $emp->save();
                    };
                    DB::commit();
                    return response(['message' => 'created'], Response::HTTP_CREATED);
                });
            } catch (\Exception $exception) {
                DB::rollback();
                return response()->json([
                    'data' => $exception,
                    'message' => 'Không thể upload, Vui lòng kiểm tra lại dữ liệu nhập',
                    'code' => 500,
                ], 500);
            }
        }
        if ($done == 0) {
            return response(['message' => 'created'], Response::HTTP_CREATED);
        }
        if ($done == 1) {
            return response(['message' => "Tỉnh thành không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 2) {
            return response(['message' => "Dữ liệu trường trực thuộc không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 3) {
            return response(['message' => "Đơn vị PCC không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 4) {
            return response(['message' => "Tên quận huyện không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 5) {
            return response(['message' => "Trùng IMEI thiết bị! Không thể nhập dữ liệu"], 500);
        }
    }

    function downloadMauDonViHoTro(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        try {
            if (isset($tinh_thanh_id)) {
                $excelFile = public_path() . '/imports/donvihotro_tinhthanh.xlsx';
            } else
                $excelFile = public_path() . '/imports/donvihotro.xlsx';
            $this->load($excelFile, 'Sheet1', function ($sheet) {
            })->download('xlsx');
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function importDonViHoTro(Request $request)
    {
        $file = $request->file('file');
        if (empty($file)) {
            return response()->json([
                'data' => [],
                'message' => 'Không tìm thấy file',
                'code' => 404,
            ], 404);
        }
        $file_id = time();
        $fileName = $file_id . '_' . $file->getClientOriginalName();
        $file->storeAs('public/imports/', $fileName);
        global $done;
        $done = 0;
        if (Storage::exists('public/imports/' . $fileName)) {
            DB::beginTransaction();
            try {
                \Excel::filter('chunk')->load(storage_path('app/public/imports/' . $fileName))->chunk(200, function ($reader) {
                    global $done;
                    foreach ($reader as $row) {
                        $user = auth()->user();
                        $info = $row->all();
                        $donViHoTro['ma'] = trim($info['ma_don_vi']);
                        $donViHoTro['ten'] = trim($info['ten_don_vi']);
                        $donViHoTro['so_dien_thoai'] = trim($info['so_dien_thoai']);
                        $donViHoTro['long'] = trim($info['kinh_do']);
                        $donViHoTro['lat'] = trim($info['vi_do']);

                        if (isset($user->tinh_thanh_id) && $user->role_id == 2) {
                            $donViHoTro['tinh_thanh_id'] =  $user->tinh_thanh_id;
                        } else {
                            if ($donViHoTro['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()) {
                                $donViHoTro['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()->id;
                            } else {
                                DB::rollback();
                                $done = 1;
                                break;
                            }
                        }
                        $donViHoTro['quan_huyen_id'] = QuanHuyen::where('tinh_thanh_id', $donViHoTro['tinh_thanh_id'])->where('name', 'ilike', trim($info['quan_huyen']))->first()->id;

                        if (DonViHoTro::where('ma', $donViHoTro['ma'])->first()) {
                            DB::rollback();
                            $done = 2;
                            break;
                        }

                        $info['loai_don_vi_ho_tro'] = trim($info['loai_don_vi_ho_tro']);
                        $donViHoTro['loai_don_vi_id'] = DanhMuc::where('parent_id', 7)->where('ten', 'ilike', $info['loai_don_vi_ho_tro'])->first()->id;

                        $emp = DonViHoTro::create($donViHoTro);
                        $emp->save();
                    };
                    DB::commit();
                    return response(['message' => 'created'], Response::HTTP_CREATED);
                });
            } catch (\Exception $exception) {
                DB::rollback();
                return response()->json([
                    'data' => $exception,
                    'message' => 'Không thể upload, Vui lòng kiểm tra lại dữ liệu nhập',
                    'code' => 500,
                ], 500);
            }
        }
        if ($done == 0) {
            return response(['message' => 'created'], Response::HTTP_CREATED);
        }
        if ($done == 1) {
            return response(['message' => "Tỉnh thành không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
        if ($done == 2) {
            return response(['message' => "Mã đơn vị đã tồn tại! Không thể nhập dữ liệu"], 500);
        }
    }

    function downloadMauDiemLayNuoc(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        try {
            if (isset($tinh_thanh_id)) {
                $excelFile = public_path() . '/imports/diemlaynuoc_tinhthanh.xlsx';
            } else
                $excelFile = public_path() . '/imports/diemlaynuoc.xlsx';
            $this->load($excelFile, 'Sheet1', function ($sheet) {
            })->download('xlsx');
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function importDiemLayNuoc(Request $request)
    {
        $file = $request->file('file');
        if (empty($file)) {
            return response()->json([
                'data' => [],
                'message' => 'Không tìm thấy file',
                'code' => 404,
            ], 404);
        }
        $file_id = time();
        $fileName = $file_id . '_' . $file->getClientOriginalName();
        $file->storeAs('public/imports/', $fileName);
        global $done;
        $done = 0;
        if (Storage::exists('public/imports/' . $fileName)) {
            DB::beginTransaction();
            try {
                set_time_limit(0);
                \Excel::filter('chunk')->load(storage_path('app/public/imports/' . $fileName))->chunk(200, function ($reader) {
                    global $done;
                    foreach ($reader as $row) {
                        $user = auth()->user();
                        $info = $row->all();
                        $diemLayNuoc['ma'] = trim($info['ma_diem_lay_nuoc']);
                        $diemLayNuoc['ma'] = 'DLN';
                        $diemLayNuoc['ten'] = trim($info['ten_diem_lay_nuoc']);
                        $diemLayNuoc['dia_chi'] = trim($info['dia_chi']);
                        $diemLayNuoc['don_vi_quan_ly_id'] = null;
                        $diemLayNuoc['description'] = trim($info['mo_ta']);
                        $diemLayNuoc['status'] = null;
                        $diemLayNuoc['kha_nang_cap_nuoc_cho_xe'] = trim($info['co_kha_nang_cap_nuoc_cho_xe_chua_chay']);
                        $diemLayNuoc['loai'] = trim($info['loai']);
                        $diemLayNuoc['long'] = trim($info['toa_do_x']);
                        $diemLayNuoc['lat'] = trim($info['toa_do_y']);
                        $diemLayNuoc['quan_huyen_id'] = null;
                        if (
                            $diemLayNuoc['ten'] == null &&
                            $diemLayNuoc['dia_chi'] == null &&
                            $info['don_vi_quan_ly'] == null &&
                            $diemLayNuoc['don_vi_quan_ly_id'] = null &&
                            $diemLayNuoc['description'] = trim($info['mo_ta']) &&
                            $diemLayNuoc['kha_nang_cap_nuoc_cho_xe'] == null &&
                            $diemLayNuoc['loai'] = null &&
                            $diemLayNuoc['long'] == null &&
                            $diemLayNuoc['lat'] == null
                        ) {
                            DB::commit();
                            return response(['message' => 'created'], Response::HTTP_CREATED);
                            break;
                        }
                        if (isset($user->tinh_thanh_id) && $user->role_id == 2) {
                            $diemLayNuoc['tinh_thanh_id'] =  $user->tinh_thanh_id;
                        } else {
                            if ($diemLayNuoc['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()) {
                                $diemLayNuoc['tinh_thanh_id'] = TinhThanh::where('name', 'ilike', trim($info['tinh_thanh']))->first()->id;
                            } else {
                                DB::rollback();
                                $done = 1;
                                break;
                            }
                        }

                        if ($diemLayNuoc['long'] && $diemLayNuoc['lat']) {
                            $proj4 = new Proj4php();
                            $projWGS84 = $diemLayNuoc['tinh_thanh_id'] == 1 ? new Proj4phpProj('EPSG:3406') :  new Proj4phpProj('EPSG:3405');
                            // $projWGS84 = new Proj4phpProj('EPSG:3405');
                            $projUTM33N = new Proj4phpProj('EPSG:4326');
                            $toaDo = $proj4->transform($projWGS84, $projUTM33N, new proj4phpPoint($diemLayNuoc['long'], $diemLayNuoc['lat']));
                            $diemLayNuoc['long'] = $toaDo->x;
                            $diemLayNuoc['lat'] = $toaDo->y;
                        } else {
                            $diemLayNuoc['long'] = 106.11014675867497;
                            $diemLayNuoc['lat'] = 20.7472047463564;
                        }


                        if (DonViPccc::where('tinh_thanh_id', $diemLayNuoc['tinh_thanh_id'])->where('ten', 'ilike', trim($info['don_vi_quan_ly']))->first()) {
                            $diemLayNuoc['don_vi_quan_ly_id'] = DonViPccc::where('tinh_thanh_id', $diemLayNuoc['tinh_thanh_id'])->where('ten', 'ilike', trim($info['don_vi_quan_ly']))->first()->id;
                        }
                        // dd(trim($info['quan_huyen']));
                        $diemLayNuoc['quan_huyen_id'] = QuanHuyen::where('tinh_thanh_id', $diemLayNuoc['tinh_thanh_id'])->where('name', 'ilike', trim($info['quan_huyen']))->first()->id;
                        if (mb_strtolower($diemLayNuoc['kha_nang_cap_nuoc_cho_xe']) == 'x') {
                            $diemLayNuoc['kha_nang_cap_nuoc_cho_xe'] = true;
                        }
                        if (mb_strtolower($diemLayNuoc['kha_nang_cap_nuoc_cho_xe']) == '0' || mb_strtolower($diemLayNuoc['kha_nang_cap_nuoc_cho_xe']) == 'o' || mb_strtolower($diemLayNuoc['kha_nang_cap_nuoc_cho_xe']) == '' || mb_strtolower($diemLayNuoc['kha_nang_cap_nuoc_cho_xe']) == null) {
                            $diemLayNuoc['kha_nang_cap_nuoc_cho_xe'] = false;
                        }
                        $emp = DiemLayNuoc::create([
                            "ma" => "DLN",
                            "ten" => $diemLayNuoc['ten'],
                            "dia_chi" => $diemLayNuoc['dia_chi'],
                            "don_vi_quan_ly_id" => $diemLayNuoc['don_vi_quan_ly_id'],
                            "description" => $diemLayNuoc['description'],
                            "status" => null,
                            "kha_nang_cap_nuoc_cho_xe" => $diemLayNuoc['kha_nang_cap_nuoc_cho_xe'],
                            "loai" => $diemLayNuoc['loai'],
                            "long" => $diemLayNuoc['long'],
                            "lat" => $diemLayNuoc['lat'],
                            "tinh_thanh_id" => $diemLayNuoc['tinh_thanh_id'],
                            "quan_huyen_id" => $diemLayNuoc['quan_huyen_id'],
                        ]);
                        // $emp = DiemLayNuoc::create($diemLayNuoc);
                        // $emp->save();
                        DiemLayNuoc::where('id', $emp->id)->update([
                            'ma' => 'ĐLN ' . TinhThanh::where('id', $diemLayNuoc['tinh_thanh_id'])->first()->code . '' . $emp->id
                        ]);
                    };
                    DB::commit();
                    return response(['message' => 'created'], Response::HTTP_CREATED);
                });
            } catch (\Exception $exception) {
                DB::rollback();
                return response()->json([
                    'data' => $exception,
                    'message' => 'Không thể upload, Vui lòng kiểm tra lại dữ liệu nhập',
                    'code' => 500,
                ], 500);
            }
        }
        if ($done == 0) {
            return response(['message' => 'created'], Response::HTTP_CREATED);
        }
        if ($done == 1) {
            return response(['message' => "Tỉnh thành không hợp lệ! Không thể nhập dữ liệu"], 500);
        }
    }
}
