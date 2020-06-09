<?php

namespace App\Http\Controllers;

use App\DiemChay;
use App\DonViPccc;
use App\Scopes\ToaNhaScope;
use App\Scopes\TinhThanhScope;
use App\ThietBi;
use Illuminate\Support\Facades\DB;
use App\TinhThanh;
use Illuminate\Http\Request;
use App\ToaNha;
use Excel;
use App\Traits\ExecuteExcel;
use App\Http\Resources\ThietBiResource;
use App\ThongBaoTrangThaiThietBi;
use App\User;
use Carbon\Carbon;
use OneSignal;
use PhpParser\Node\Stmt\Do_;

class BaoCaoController extends Controller
{
    use ExecuteExcel;
    public function excelThietBiTinhThanh($id, Request $request)
    {
        $query = ToaNha::query()->where('tinh_thanh_id', $id)->with(['tinhThanh', 'quanHuyen', 'soDienThoai', 'donViPccc', 'loaiHinhSoHuu', 'thietBi']);
        $thietbi_array[] = array('Tên tòa nhà', 'Tỉnh thành', 'Địa chỉ', 'Số điện thoại', 'Đơn vị PCCC', 'Loại hình sở hữu', 'Số lượng thiết bị');
        $query->each(function ($thietBi) use (&$thietbi_array) {
            $thietbi_array[] = array(
                'Tên tòa nhà'   => $thietBi->ten,
                'Tỉnh thành'    => $thietBi->tinhThanh->name,
                'Địa chỉ'  => $thietBi->dia_chi,
                'Số điện thoại' => count($thietBi->soDienThoai) > 0 ? $thietBi->soDienThoai[0]->so_dien_thoai : null,
                'Đơn vị PCCC' => $thietBi->donViPccc->ten,
                'Loại hình sở hữu' => $thietBi->loaiHinhSoHuu != null ? $thietBi->loaiHinhSoHuu->ten : null,
                'Số lượng thiết bị' => count($thietBi->thietBi),
            );
        });
        Excel::create('Báo cáo thiết bị', function ($excel) use ($thietbi_array) {
            $excel->setTitle('Báo cáo thiết bị');
            $excel->sheet('Báo cáo thiết bị', function ($sheet) use ($thietbi_array) {
                $sheet->fromArray($thietbi_array, null, 'A1', false, false);
                $j = 'A';
                $x = 1;
                for ($i = 0; $i < count($thietbi_array); $i++) {
                    $sheet->getStyle($j . $x)->applyFromArray(array(
                        'font' => array(
                            'bold'      =>  true,
                            'size'  => 12,
                        )
                    ));
                    $j++;
                }
            });
        })->download('xlsx');
    }
    public function excelDiemChayTinhThanh($id)
    {
        $diemchay_data = DiemChay::query()->where('tinh_thanh_id', $id)->with(['trangThaiDiemChay', 'toaNha', 'tinhThanh'])->get();
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

    public function excelCanBoThamGiaChuaChay($id)
    {
        $diemchay_data = DiemChay::query()->where('tinh_thanh_id', $id)->with(['trangThaiDiemChay', 'toaNha', 'tinhThanh'])->get();
        $diemchay_array[] = array('STT', 'Tên', 'Địa chỉ', 'Tòa Nhà', 'Nguyên nhân', 'Ước tính thiệt hại', 'Số người chết', 'Số người bị thương', 'Số cán bộ tham gia chữa cháy', 'Ghi chú');
        $count = 0;
        foreach ($diemchay_data as $key => $diemchay) {
            $count++;
            $diemchay_array[] = array(
                'STT' => $key + 1,
                'Tên'  => $diemchay->ten,
                'Địa chỉ'  => $diemchay->dia_chi,
                'Tòa Nhà' => $diemchay->toaNha['ten'],
                'Nguyên nhân' => $diemchay->nguyen_nhan,
                'Ước tính thiệt hại' => $diemchay->uoc_tinh_thiet_hai,
                'Số người chết' => str_replace(' ', '', $diemchay->so_nguoi_chet),
                'Số người bị thương' => str_replace(' ', '', $diemchay->so_nguoi_bi_thuong),
                'Số cán bộ tham gia chữa cháy' => $diemchay->so_nguoi_tham_gia_chua_chay,
                'Ghi chú' => $diemchay->mo_ta
            );
        }
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
            });
        })->download('xlsx');
    }

    public function getThietBiTheoTinh()
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }
        if ($user->role_id == 2 && $user->tinh_thanh_id) {
            $tinh = TinhThanh::where('id', $user->tinh_thanh_id)->select('id', 'name')->orderBy('rate', 'ASC')->get();
        }
        if ($user->role_id == 1) {
            $tinh = TinhThanh::select('id', 'name')->orderBy('rate', 'ASC')->get();
        }
        if (isset($tinh)) {
            foreach ($tinh as $item) {

                $item['so_thiet_bi'] = ThietBi::withoutGlobalScope(ToaNhaScope::class)->with('toaNha')->select('id', 'ten', 'toa_nha_id', 'imei')->whereHas('toaNha', function ($query) use ($item) {
                    $query->where('tinh_thanh_id', $item->id);
                })->count();

                $item['tam'] = TinhThanh::where('id', $item->id)->select(DB::raw('st_asgeojson(st_centroid(geom))'))->first();
                if ($item->id === 23) {
                    $item['tam']["st_asgeojson"] =
                        "{\"type\":\"Point\",\"coordinates\":[105.854092,21.026119]}";
                };
            };

            return \response($tinh, 200);
        }
    }
    public function getTrangThaiThietBi()
    {
        $query = ThietBi::withoutGlobalScope(ToaNhaScope::class)->select('id', 'ten', 'toa_nha_id', 'imei')->get();
        $json = file_get_contents('http://171.244.49.26:2585/SAS/GetDeviceByUser/123456');
        $objs = collect(json_decode($json));
        foreach ($query as $item) {
            $item['online'] = false;
            $thietBi = $objs->where('IMEI', $item->imei)->first();
            if ($thietBi) {
                $item['thiet_bi'] = $thietBi;
                $item['online'] = true;
                $item['battery'] = $thietBi->Battery;
            }
            $toaNha = ToaNha::withoutGlobalScope(TinhThanhScope::class)->where('id', $item->toa_nha_id)->first();
            $item['toa_nha'] = $toaNha->ten;
            $item['tinh_thanh'] = TinhThanh::where('id', $toaNha->tinh_thanh_id)->first()->name;
        };

        return \response($query, 200);
    }
    public function getDataBieuDoThietBi()
    {
        $data = [];
        $now = Carbon::now();
        for ($i = 1; $i <= $now->month; $i++) {
            $thang = "Tháng " . $i;
            $thietbi_namtruoc = ThietBi::whereYear('created_at', '<', $now->year)->count();
            $thietbi = ThietBi::whereYear('created_at', '=', $now->year)->whereMonth('created_at', '<=', $i)->count();
            array_push($data, [$thang, $thietbi + $thietbi_namtruoc]);
        }
        return \response([$data], 200);
    }

    public function getDataBieuDoVuChay()
    {
        $data = [];
        $now = Carbon::now();
        for ($i = 1; $i <= $now->month; $i++) {
            $thang = "Tháng " . $i;
            $thietbi = DiemChay::whereYear('created_at', '=', $now->year)->whereMonth('created_at', '=', $i)->count();
            array_push($data, [$thang, $thietbi]);
        }
        return \response([$data], 200);
    }
    public function getDataBieuDoThietHai()
    {
        $data = [];
        $now = Carbon::now();
        for ($i = 1; $i <= $now->month; $i++) {
            $thang = "Tháng " . $i;
            $thietbi = DiemChay::whereYear('created_at', '=', $now->year)->whereMonth('created_at', '=', $i)->get();
            $tongThietHai = 0;
            foreach ($thietbi as $item) {
                if ($item->uoc_tinh_thiet_hai) {
                    $tongThietHai = $tongThietHai + (float) $item->uoc_tinh_thiet_hai / 1000000;
                }
            };
            array_push($data, [$thang, round($tongThietHai, 2)]);
        }
        return \response([$data], 200);
    }

    public function getSoThietBiOnlineOffline(Request $request)
    {
        $user = auth()->user();
        $tinh_thanh = $request->get('tinh_thanh_id');
        if (!$user) {
            return;
        }
        $online = 0;
        $offline = 0;
        $query = ThietBi::select('id', 'ten', 'toa_nha_id', 'imei');
        if (isset($tinh_thanh)) {
            $toaNha = ToaNha::where('tinh_thanh_id', $tinh_thanh)->pluck('id');
            $query = $query->whereIn('toa_nha_id', $toaNha);
        }
        $query = $query->get();
        $json = file_get_contents('http://171.244.49.26:2585/SAS/GetDeviceByUser/123456');
        $objs = collect(json_decode($json));
        foreach ($query as $item) {
            $thietBi = $objs->where('IMEI', $item->imei)->first();
            if ($thietBi) {
                $online++;
            } else {
                $offline++;
            }
        };
        $thongBaoMoi = ThongBaoTrangThaiThietBi::where('trang_thai_da_doc', false)->count();
        return \response()->json([
            'online' => $online,
            'offline' => $offline,
            'thongbaomoi' => $thongBaoMoi
        ], 200);
    }
    public function getThongBao(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->get('perPage', 10);
        if ($user != null) {
            $query = ThietBi::select('id', 'ten', 'toa_nha_id', 'imei')->get();
            $json = file_get_contents('http://171.244.49.26:2585/SAS/GetDeviceByUser/123456');
            $objs = collect(json_decode($json));
            foreach ($query as $item) {
                $item['online'] = false;
                $thietBi = $objs->where('IMEI', $item->imei)->first();
                if ($thietBi) {
                    $item['thiet_bi'] = $thietBi;
                    $item['online'] = true;
                    $item['battery'] = $thietBi->Battery;
                }
                if (!ThongBaoTrangThaiThietBi::where('thiet_bi_id', $item->id)->first()) {
                    ThongBaoTrangThaiThietBi::create([
                        'thiet_bi_id' => $item->id,
                        'trang_thai_da_doc' => false,
                        'noi_dung' => 'Thiết bị ' . $item->ten . ' Được khởi tạo',
                        'toa_nha_id' => $item->toa_nha_id ? $item->toa_nha_id : null,
                        'tinh_thanh_id' => $item->toa_nha_id ? TinhThanh::where('id', ToaNha::where('id', $item->toa_nha_id)->first()->tinh_thanh_id)->first()->id : null
                    ]);
                }
                if (ThongBaoTrangThaiThietBi::where('thiet_bi_id', $item->id)->where('trang_thai', 'Online')->first()) {
                    if (ThongBaoTrangThaiThietBi::where('thiet_bi_id', $item->id)->orderBy('updated_at', 'DESC')->first()->trang_thai == 'Online' && !$item['online']) {
                        ThongBaoTrangThaiThietBi::where('thiet_bi_id', $item->id)->where('trang_thai', 'Offline')->update([
                            'trang_thai_da_doc' => false,
                            'noi_dung' => 'Thiết bị ' . $item->ten . ' Đã Offline',
                            'toa_nha_id' => $item->toa_nha_id ? $item->toa_nha_id : null,
                            'tinh_thanh_id' => $item->toa_nha_id ? TinhThanh::where('id', ToaNha::where('id', $item->toa_nha_id)->first()->tinh_thanh_id)->first()->id : null
                        ]);
                        if ($item->toa_nha_id) {
                            $userMobile =  User::where('toa_nha_id', $item->toa_nha_id)->get();
                            $time = ThongBaoTrangThaiThietBi::where('thiet_bi_id', $item->id)->where('trang_thai', 'Offline')->first()->updated_at;
                            foreach ($userMobile as $el) {

                                OneSignal::sendNotificationUsingTags(
                                    "Thiết bị " . $item->ten . " đã offline! lúc " .  $time->format('H:i d/m/Y'),
                                    array(["field" => "tag", "relation" => "=", "key" => "user_id", "value" => $el->id]),
                                    $url = null,
                                    $data = ['type' => 'task_new', 'id' => $el->id]
                                );
                            }
                        }
                    }
                    if (ThongBaoTrangThaiThietBi::where('thiet_bi_id', $item->id)->orderBy('updated_at', 'DESC')->first()->trang_thai == 'Offline' && $item['online']) {
                        ThongBaoTrangThaiThietBi::where('thiet_bi_id', $item->id)->where('trang_thai', 'Online')->update([
                            'thiet_bi_id' => $item->id,
                            'trang_thai_da_doc' => false,
                            'noi_dung' => 'Thiết bị ' . $item->ten . ' Đã Online',
                            'toa_nha_id' => $item->toa_nha_id ? $item->toa_nha_id : null,
                            'tinh_thanh_id' => $item->toa_nha_id ? TinhThanh::where('id', ToaNha::where('id', $item->toa_nha_id)->first()->tinh_thanh_id)->first()->id : null
                        ]);
                        if ($item->toa_nha_id) {
                            $userMobile =  User::where('toa_nha_id', $item->toa_nha_id)->get();
                            $time = ThongBaoTrangThaiThietBi::where('thiet_bi_id', $item->id)->where('trang_thai', 'Online')->first()->updated_at;
                            foreach ($userMobile as $el) {

                                OneSignal::sendNotificationUsingTags(
                                    "Thiết bị " . $item->ten . " đã Online! lúc " . $time->format('H:i d/m/Y'),
                                    array(["field" => "tag", "relation" => "=", "key" => "user_id", "value" => $el->id]),
                                    $url = null,
                                    $data = ['type' => 'task_new', 'id' => $el->id]
                                );
                            }
                        }
                    }
                } else {
                    if ($item['online']) {
                        ThongBaoTrangThaiThietBi::create([
                            'thiet_bi_id' => $item->id,
                            'trang_thai_da_doc' => false,
                            'noi_dung' => 'Thiết bị ' . $item->ten . ' Đã Online',
                            'trang_thai' => "Online",
                            'toa_nha_id' => $item->toa_nha_id ? $item->toa_nha_id : null,
                            'tinh_thanh_id' => $item->toa_nha_id ? TinhThanh::where('id', ToaNha::where('id', $item->toa_nha_id)->first()->tinh_thanh_id)->first()->id : null
                        ]);
                        if ($item->toa_nha_id) {
                            $userMobile =  User::where('toa_nha_id', $item->toa_nha_id)->get();
                            $time = ThongBaoTrangThaiThietBi::where('thiet_bi_id', $item->id)->where('trang_thai', 'Online')->first()->updated_at;
                            foreach ($userMobile as $el) {

                                OneSignal::sendNotificationUsingTags(
                                    "Thiết bị " . $item->ten . " đã Online! lúc " . $time->format('H:i d/m/Y'),
                                    array(["field" => "tag", "relation" => "=", "key" => "user_id", "value" => $el->id]),
                                    $url = null,
                                    $data = ['type' => 'task_new', 'id' => $el->id]
                                );
                            }
                        }
                    }
                }
            };
            $thietBis = ThongBaoTrangThaiThietBi::with('toaNha', 'tinhThanh')->take($perPage);
            if ($user->role_id == 1) {
                $thietBis = $thietBis->orderBy('updated_at', 'DESC')->get();
            }
            if ($user->role_id == 2 && $user->tinh_thanh_id) {
                $thietBis = $thietBis->where('tinh_thanh_id', $user->tinh_thanh_id)->orderBy('updated_at', 'DESC')->get();
            }
            if (($user->role_id == 3 || $user->role_id == 4) && $user->toa_nha_id) {
                $thietBis = $thietBis->where('toa_nha_id', $user->toa_nha_id)->orderBy('updated_at', 'DESC')->get();
            }
            return \response($thietBis, 200);
        } else return;
    }
    public function docThongBao()
    {
        try {
            ThongBaoTrangThaiThietBi::where('trang_thai_da_doc', false)->update([
                'trang_thai_da_doc' => true
            ]);
            return response([], 200);
        } catch (\Exception $e) {
            return response($e, 500);
        }
    }

    public function senNotify()
    {
        OneSignal::sendNotificationUsingTags(
            "VKL Thiết bị đã offline!",
            array(["field" => "tag", "relation" => "=", "key" => "user_id", "value" => 57]),
            $url = null,
            $data = ['type' => 'task_new', 'id' => 57]
        );
        return response(['message' => 'Thanh cong'], 200);
    }
    public function getDiemChay(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $tong = DiemChay::query()->count();
        $query = DiemChay::query();
        if (isset($tinh_thanh_id)) {
            $query = DiemChay::where('tinh_thanh_id', $tinh_thanh_id);
            $tong = DiemChay::where('tinh_thanh_id', $tinh_thanh_id)->count();
        }
        $query = $query->orderBy('updated_at', 'desc');
        $tinhs = $query->take($perPage)->get();
        return response()->json([
            'data' => $tinhs,
            'tong' => $tong,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function getDonVi(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $tong = DonViPccc::count();
        $query = DonViPccc::query();
        $user = auth()->user();
        if (!$user) {
            return;
        }
        if (isset($tinh_thanh_id)) {
            $query = $query->where('tinh_thanh_id', $tinh_thanh_id);
            $tong = DonViPccc::where('tinh_thanh_id', $tinh_thanh_id)->count();
        }
        if ($user->role_id == 2 && $user->tinh_thanh_id) {
            $query = $query->where('tinh_thanh_id', $user->tinh_thanh_id);
            $tong = DonViPccc::where('tinh_thanh_id', $user->tinh_thanh_id)->count();
        }
        $query->orderBy('updated_at', 'desc');

        $tinhs = $query->take($perPage)->get();

        return response()->json([
            'data' => $tinhs,
            'tong' => $tong,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function getToaNha(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $user = auth()->user();
        if (!$user) {
            return;
        }
        $tong = ToaNha::count();
        $query = ToaNha::query();
        if (isset($tinh_thanh_id)) {
            $query = $query->where('tinh_thanh_id', $tinh_thanh_id);
            $tong = ToaNha::where('tinh_thanh_id', $tinh_thanh_id)->count();
        }
        if ($user->role_id == 2 && $user->tinh_thanh_id) {
            $query = $query->where('tinh_thanh_id', $user->tinh_thanh_id);
            $tong = ToaNha::where('tinh_thanh_id', $user->tinh_thanh_id)->count();
        }
        $query->orderBy('updated_at', 'desc');

        $tinhs = $query->take($perPage)->get();

        return response()->json([
            'data' => $tinhs,
            'tong' => $tong,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }
    public function getDiemDangChay(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $user = auth()->user();
        $query = DiemChay::where('trang_thai', 'dang_chay');
        $query->orderBy('updated_at', 'desc');
        $tinhs = $query->get();
        if (isset($tinh_thanh_id)) {
            $query = $query->where('tinh_thanh_id', $tinh_thanh_id);
        }
        if (!$user) {
            return;
        }
        if ($user->role_id == 2 && $user->tinh_thanh_id) {
            $query = $query->where('tinh_thanh_id', $user->tinh_thanh_id);
        }
        if (!$user) {
            return;
        }
        return response()->json([
            'data' => $tinhs,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function getPolygon(Request $request)
    {
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $tinh_thanh = null;
        if (isset($tinh_thanh_id)) {
            $tinh_thanh = TinhThanh::where('id', $tinh_thanh_id)->select(DB::raw('st_asgeojson(geom)'))->first();
        }
        return response()->json([
            'data' => $tinh_thanh,
            'code' => 200,
            'message' => 'Thành công'
        ], 200);
    }
    public function getThongmobile(Request $request)
    {
        $user = auth()->user();
        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 10);
        $thietBis = [];
        if ($user && ($user->role_id == 3 || $user->role_id == 4) && $user->toa_nha_id) {
            $thietBiToaNha = ThietBi::where('toa_nha_id', $user->toa_nha_id)->pluck('id');
            $thietBis = ThongBaoTrangThaiThietBi::with('toaNha', 'tinhThanh')->where('toa_nha_id', $user->toa_nha_id)->whereIn('thiet_bi_id', $thietBiToaNha)->orderBy('updated_at', 'DESC')->paginate($perPage, ['*'], 'page', $page);
        }
        return response($thietBis, 200);
    }
}
