<?php

namespace App\Http\Controllers;

use App\DanhMucSanPham;
use App\DonDatHang;
use App\HangTonKho;
use App\HinhAnhSanPham;
use App\SanPham;
use App\SanPhamDonDatHang;
use App\Scopes\ActiveScope;
use App\ThietBi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ExecuteExcel;

class SanPhamController extends Controller
{
    use ExecuteExcel;
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
            }
            elseif (isset($failedRules['file']['max'])) {
                $message = 'Kích thước tệp quá lớn';
            }

            return response()->json([
                'message' => $message,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $image = $request->file;
        $name = time() . '.' . $image->getClientOriginalExtension();
        $image->move('storage/images/avatar/', $name);
        $file = HinhAnhSanPham::create([
            'san_pham_id' => null,
            'url_hinh_anh' => 'storage/images/avatar/' . $name
        ]);
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'result' => $file->id,
        ]);
    }
    public function addSanPham(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'ten_san_pham' => 'required',
            'danh_muc_id' => 'required',
            'don_vi_tinh' => 'required',
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
        $files = $data['fileList'];
        try {
            $sanPham =  SanPham::create([
                'ten_san_pham' => $data['ten_san_pham'],
                'danh_muc_id' => $data['danh_muc_id'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'ma' => time(),
                'gia_ban' => $data['gia_ban'],
                'gia_sale' => $data['gia_sale'],
                'don_vi_tinh' => $data['don_vi_tinh'],
                'gia_von' => $data['gia_von'],
                'mo_ta_san_pham' => $data['mo_ta_san_pham'],
                'vi_tri' => $data['vi_tri'],
                'thuong_hieu_id' => $data['thuong_hieu_id'],
                'thoi_gian_bao_quan' => $data['thoi_gian_bao_quan'],
                'ton_kho_thap_nhat' => $data['ton_kho_thap_nhat']
            ]);
            if (count($files) > 0) {
                foreach ($files as $item) {
                    if (!empty($item['response']['result'])) {
                        HinhAnhSanPham::where('id', $item['response']['result'])->update(['san_pham_id' => $sanPham->id]);
                    }
                }
            }
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm sản phẩm'], 500);
        }
    }

    public function getSanPham(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $query = SanPham::with('danhMuc', 'sanPhamTonKho:san_pham_id,so_luong', 'thuongHieu:id,ten');
        $search = $request->get('search');
        $danh_muc_id = $request->get('danh_muc_id');
        $san_pham_id = $request->get('san_pham_id');
        if (isset($san_pham_id) && count($san_pham_id) > 0) {
            $query->whereIn('id', $san_pham_id);
        }
        if (isset($danh_muc_id)) {
            $query->where('danh_muc_id', $danh_muc_id);
        }
        if (isset($search)) {
            $search = trim($search);
            $query->where(\DB::raw('CONCAT(unaccent(ten_san_pham), ten_san_pham)'), 'ilike', "%{$search}%");
            $query->orWhere(\DB::raw('CONCAT(unaccent(mo_ta_san_pham), mo_ta_san_pham)'), 'ilike', "%{$search}%");

            // $query->whereRaw('CONCAT(unaccent(ten_san_pham), ten_san_pham) ilike ' . "'%{$search}%'");
            // $query->orWhereRaw('CONCAT(unaccent(mo_ta_san_pham), mo_ta_san_pham) ilike ' . "'%{$search}%'");
            if(strlen($search) > 5){
                $itemCode = substr($search, 0, 6); 
                if(is_numeric($itemCode)){
                    $itemCode = (int)$itemCode;
                    $query = SanPham::with('danhMuc', 'sanPhamTonKho:san_pham_id,so_luong', 'thuongHieu:id,ten');
                    $query->where('item_code', $itemCode);
                }
            }
        }

        $query->orderBy('updated_at', 'desc');
        $dancu = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'data' => $dancu,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function xoaSanPham($id)
    {
        try {
            SanPham::find($id)->update(['active' => false]);

            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa sản phẩm này'], 500);
        }
    }

    public function getSanPhamDetail($id)
    {
        $sanPham = SanPham::where('id', $id)->with('hinhAnhs', 'danhMuc:id,ten_danh_muc')->first();
        return response($sanPham, 200);
    }
    public function uploadEdit($id, Request $request)
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
            }
            elseif (isset($failedRules['file']['max'])) {
                $message = 'Kích thước tệp quá lớn';
            }

            return response()->json([
                'message' => $message,
                'data' => [
                    $validator->errors()->all(),
                ],
            ], 400);
        }
        $image = $request->file;
        $name = time() . '.' . $image->getClientOriginalExtension();
        $image->move('storage/images/avatar/', $name);
        $file = HinhAnhSanPham::create([
            'san_pham_id' => $id,
            'url_hinh_anh' => 'storage/images/avatar/' . $name
        ]);
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'result' => $file->id,
        ]);
    }

    public function xoaAnhSanPham(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return response()->json([
                'code' => 400,
                'message' => __('ID Hình ảnh không tồn tại'),
            ], 400);
        }
        try {
            HinhAnhSanPham::find($id)->delete();
            return response(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể xóa hình ảnh'], 500);
        }
    }

    public function editSanPham($id, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'ten_san_pham' => 'required',
            'danh_muc_id' => 'required',
            'don_vi_tinh' => 'required',
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
        $files = $data['fileList'];
        try {
            $sanPham =  SanPham::where('id', $id)->update([
                'ten_san_pham' => $data['ten_san_pham'],
                'danh_muc_id' => $data['danh_muc_id'],
                'anh_dai_dien' => $data['anh_dai_dien'],
                'gia_ban' => $data['gia_ban'],
                'gia_sale' => $data['gia_sale'],
                'don_vi_tinh' => $data['don_vi_tinh'],
                'gia_von' => $data['gia_von'],
                'mo_ta_san_pham' => $data['mo_ta_san_pham'],
                'vi_tri' => $data['vi_tri'],
                'thuong_hieu_id' => $data['thuong_hieu_id'],
                'thoi_gian_bao_quan' => $data['thoi_gian_bao_quan'],
                'ton_kho_thap_nhat' => $data['ton_kho_thap_nhat']
            ]);
        } catch (\Exception $e) {
            return response(['message' => 'Không thể thêm sản phẩm'], 500);
        }
    }

    public function getSanPhamDetailTrangChu($id)
    {
        $sanPham = SanPham::where('id', $id)->with('hinhAnhs', 'danhMuc:id,ten_danh_muc', 'thuongHieu', 'sanPhamTonKho:san_pham_id,so_luong', 'nguyenLieus', 'nguyenLieus.nguyenLieus', 'nguyenLieus.nguyenLieuTonKho:san_pham_id,so_luong')->first();
        return response($sanPham, 200);
    }
    public function getSanPhamGioHang(Request $request)
    {
        $data = $request->get('san_pham_id');
        if (!isset($data) || count($data) < 1) {
            return [];
        }
        $sanPham =  SanPham::with('danhMuc', 'sanPhamTonKho:san_pham_id,so_luong')->whereIn('id', $data)->get();
        return $sanPham;
    }

    public function getSanPhamBanChay()
    {
        $hoaDon = DonDatHang::where('trang_thai', 'hoa_don')->pluck('id')->toArray();
        $sanPhams = SanPhamDonDatHang::with('sanPham:id,ten_san_pham,anh_dai_dien,gia_ban', 'sanPham.sanPhamTonKho:san_pham_id,so_luong')->select('id', 'san_pham_id', 'doanh_thu')->whereIn('don_dat_hang_id', $hoaDon)->get();
        $sanPhams =  collect($sanPhams)->unique('san_pham_id')->values()->all();
        foreach ($sanPhams as $item) {
            $query = SanPhamDonDatHang::whereIn('don_dat_hang_id', $hoaDon);
            $doanhThu = 0;
            $doanhThu = $query->where('san_pham_id', $item->san_pham_id)->sum('doanh_thu');
            $item['tong_doanh_thu'] = $doanhThu;
        };
        $sanPhams =  collect($sanPhams)->sortByDesc('tong_doanh_thu')->values()->take(20);
        return $sanPhams;
    }

    public function getSanPhamGioHangMobile(Request $request)
    {
        $data = $request->get('san_pham_id');
        if (!isset($data) || count($data) < 1) {
            return [];
        }
        $sanPham =  SanPham::with('danhMuc', 'sanPhamTonKho:san_pham_id,so_luong')->whereIn('id', $data)->get();
        return $sanPham;
    }

    public function importSanPham(Request $request)
    {

        $file = $request->file('file');
        if (!$file) {
            return response(['message' => 'Không tìm thấy file'], 404);
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
                        $info = $row->all();
                        $thietBi['ten_san_pham'] = trim($info['ten_san_pham']);
                        $thietBi['danh_muc_id'] = trim($info['nhom_hang_hoa']);
                        $thietBi['gia_ban'] = (int)trim($info['gia_ban']);
                        $thietBi['don_vi_tinh'] = trim($info['don_vi_tinh']);
                        $thietBi['gia_von'] = (int)trim($info['gia_von']);
                        $thietBi['vi_tri'] = trim($info['vi_tri']);
                        $thietBi['ma'] = trim($info['ma']);
                        $thietBi['ton_kho_thap_nhat'] = (int)trim($info['ton_kho_thap_nhat']);
                        $thietBi['thoi_gian_bao_quan'] = (int)trim($info['thoi_gian_bao_quan']);
                        $thietBi['item_code'] = (int)trim($info['item_code']);

                        if (isset($info['nhom_hang_hoa']) && isset($thietBi['ten_san_pham']) && isset($thietBi['don_vi_tinh'])) {
                            $nhomSanPham = DanhMucSanPham::where('ten_danh_muc', 'ilike', $info['nhom_hang_hoa'])->first();

                            if (!$nhomSanPham) {
                                DB::rollback();
                                $done = 1;
                                break;
                            } else {
                                $thietBi['danh_muc_id'] =  $nhomSanPham->id;
                            }
                            if (isset($thietBi['ton_kho_thap_nhat']) && !is_numeric($thietBi['ton_kho_thap_nhat']) && $thietBi['ton_kho_thap_nhat'] != 0) {
                                DB::rollback();
                                $done = 2;
                                break;
                            }
                            if (isset($thietBi['thoi_gian_bao_quan']) && !is_numeric($thietBi['thoi_gian_bao_quan']) && $thietBi['thoi_gian_bao_quan'] != 0) {
                                DB::rollback();
                                $done = 3;
                                break;
                            }
                            if (isset($thietBi['gia_ban']) && !is_numeric($thietBi['gia_ban']) && $thietBi['gia_ban'] != 0) {
                                DB::rollback();
                                $done = 4;
                                break;
                            }
                            if (isset($thietBi['gia_von']) && !is_numeric($thietBi['gia_von']) && $thietBi['gia_von'] != 0) {
                                DB::rollback();
                                $done = 5;
                                break;
                            }
                            if (!isset($thietBi['ma'])) {
                                DB::rollback();
                                $done = 6;
                                break;
                            }
                
                            $sp = SanPham::where('ma', $thietBi['ma'])->first();
                            if ($sp) {
                                $sp->update([
                                    'ten_san_pham' => $thietBi['ten_san_pham'],
                                    'danh_muc_id' => $thietBi['danh_muc_id'],
                                    'ma' => $thietBi['ma'],
                                    'gia_ban' => $thietBi['gia_ban'],
                                    'don_vi_tinh' => $thietBi['don_vi_tinh'],
                                    'gia_von' => $thietBi['gia_von'] ? $thietBi['gia_von'] : null,
                                    'vi_tri' => $thietBi['vi_tri'],
                                    'thoi_gian_bao_quan' => $thietBi['thoi_gian_bao_quan'] ? $thietBi['thoi_gian_bao_quan'] : null,
                                    'ton_kho_thap_nhat' => $thietBi['ton_kho_thap_nhat'] ? $thietBi['ton_kho_thap_nhat'] : null,
                                    'item_code'=>$thietBi['item_code'] ? $thietBi['item_code'] : null
                                ]);
                            } else {
                                $emp = SanPham::create($thietBi);
                                $emp->save();
                            }
                        }
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
        $message = "";
        $code = 200;
        switch ($done) {
            case 0:
                $message = 'Thành công';
                $code = 200;
                break;
            case 1:
                $message = 'Nhóm hàng hóa không hợp lệ';
                $code = 500;
                break;
            case 2:
                $message = 'Số tồn kho thấp nhất không hợp lệ!';
                $code = 500;
                break;
            case 3:
                $message = 'Số thời gian bảo quản không hợp lệ!';
                $code = 500;
                break;
            case 4:
                $message = 'Giá bán không hợp lệ!';
                $code = 500;
                break;
            case 5:
                $message = 'Giá vốn không hợp lệ!';
                $code = 500;
                break;
            case 6:
                $message = 'Mã sản phẩm không thể bỏ trống!';
                $code = 500;
                break;
        }
        return response(['message' => $message], $code);
    }

    function downloadMauSanPham()
    {
        try {
            $excelFile = public_path() . '/imports/sanpham.xlsx';
            $this->load($excelFile, 'Sheet1', function ($sheet) {
            })->download('xlsx');
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function exportSanPham()
    {
        $diemchay_data = SanPham::with(['danhMuc'])->get();
        $diemchay_array[] = array('STT', 'Mã *', 'Tên sản phẩm *', 'Nhóm hàng hóa *', 'Giá bán', 'Đơn vị tính *', 'Giá vốn', 'Vị trí', 'Tồn kho thấp nhất', 'Thời gian bảo quản', 'Item Code');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Mã *' => $diemchay->ma,
                'Tên sản phẩm *'  => $diemchay->ten_san_pham,
                'Nhóm hàng hóa *'  => $diemchay->danhMuc ? $diemchay->danhMuc->ten_danh_muc : "",
                'Giá bán' => $diemchay->gia_ban,
                'Đơn vị tính *' => $diemchay->don_vi_tinh,
                'Giá vốn' => $diemchay->gia_von,
                'Vị trí' => $diemchay->vi_tri,
                'Tồn kho thấp nhất' => $diemchay->ton_kho_thap_nhat,
                'Thời gian bảo quản' => $diemchay->thoi_gian_bao_quan,
                'Item code' => $diemchay->item_code
            );
        }
        \Excel::create('Sản phẩm', function ($excel) use ($diemchay_array) {
            $excel->setTitle('Sản phẩm');
            $excel->sheet('Sản phẩm', function ($sheet) use ($diemchay_array) {
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
            });
        })->download('xlsx');
    }

    public function updateMaSanPham()
    {
        $sanPhams = SanPham::withoutGlobalScope(ActiveScope::class)->get();
        foreach ($sanPhams as $item) {
            if (!$item->ma) {
                $item->update(['ma' => 'SPHH' . $item->id]);
            }
        }
        return 'Thanh Cong';
    }
}
