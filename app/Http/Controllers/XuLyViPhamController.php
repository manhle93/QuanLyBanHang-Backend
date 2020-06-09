<?php

namespace App\Http\Controllers;

use App\DiemLayNuoc;
use App\XuLyViPham;
use Illuminate\Http\Request;
use App\NhomHanhViViPham;
use App\ViPhamNhomHanhVi;
use Validator;
use Carbon\Carbon;

class XuLyViPhamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNhomHanhVi()
    {
        $nhomHanhVi = NhomHanhViViPham::get();
        return response($nhomHanhVi, 200);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $user = auth()->user();
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $tinh_thanh_id = $request->get('tinh_thanh_id');
        $thoi_gian = $request->get('thoi_gian');
        $kiem_tra = XuLyViPham::query()->with('files', 'nhomHanhVis', 'toaNha:id,ten');
        
        if (isset($user->tinh_thanh_id)) {
            $kiem_tra->where('tinh_thanh_id', $user->tinh_thanh_id);
        }
        if (isset($search)) {
            $search = trim($search);
            $kiem_tra->where(function ($kiem_tra) use ($search) {
                $kiem_tra->where('noi_dung', 'ilike', "%{$search}%");
                $kiem_tra->orWhere('doi_tuong_vi_pham', 'ilike', "%{$search}%");
            });
        }

        if (isset($tinh_thanh_id)) {
            $kiem_tra->where('tinh_thanh_id', $user->tinh_thanh_id);
        }
        if (isset($thoi_gian)) {
            $kiem_tra->where('thoi_gian', '>=', Carbon::parse($thoi_gian[0])->timezone('Asia/Ho_Chi_Minh')->startOfDay())
                ->where('thoi_gian', '<=', Carbon::parse($thoi_gian[1])->timezone('Asia/Ho_Chi_Minh')->endOfDay());
        }
        $kiem_tra->orderBy('updated_at', 'desc');
        $data = $kiem_tra->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $data,
            'message' => 'Lấy dữ liệu thành công',
            'code' => '200',
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'thoi_gian'  => 'required',
            'noi_dung_hanh_vi'  => 'required',
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
        $data['thoi_gian'] =  Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
        if (count($data['thoi_gian_tam_dinh_chi']) == 2) {
            $data['ngay_tam_dinh_chi'] =  Carbon::parse($data['thoi_gian_tam_dinh_chi'][0])->timezone('Asia/Ho_Chi_Minh');
            $data['ngay_phuc_hoi'] =  Carbon::parse($data['thoi_gian_tam_dinh_chi'][1])->timezone('Asia/Ho_Chi_Minh');
        } else {
            $data['ngay_tam_dinh_chi'] =  null;
            $data['ngay_phuc_hoi'] =  null;
        }
        try {
            $viPham = XuLyViPham::create([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'thoi_gian' => $data['thoi_gian'],
                'doi_tuong_vi_pham' => $data['doi_tuong_vi_pham'],
                'noi_dung_hanh_vi' => $data['noi_dung_hanh_vi'],
                'dinh_chi' => $data['dinh_chi'],
                'tam_dinh_chi' => $data['tam_dinh_chi'],
                'phat_tien' => $data['phat_tien'],
                'xu_ly_khac' => $data['xu_ly_khac'],
                'canh_cao' => $data['canh_cao'],
                'ngay_tam_dinh_chi' => $data['ngay_tam_dinh_chi'],
                'ngay_phuc_hoi' => $data['ngay_phuc_hoi'],
                'toa_nha_id' => $data['toa_nha_id']
            ]);
            $files = $data['fileList'];
            foreach ($files as $item) {
                if (!empty($item['response']['result'])) {
                    \App\File::where('id', $item['response']['result'])->update(['reference_id' => $viPham->id]);
                }
            }
            if (count($data['nhom_hanh_vi_id']) > 0) {
                foreach ($data['nhom_hanh_vi_id'] as $it) {
                    ViPhamNhomHanhVi::create([
                        'vi_pham_id' => $viPham->id,
                        'nhom_hanh_vi_id' => $it
                    ]);
                }
            }
            return response()->json([
                'message' => 'Thêm mới thành công',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e,
                'message' => 'Không thể thêm mới',
            ], 500);
        }
    }

    public function anTruNuoc(){

    //    $d = DiemLayNuoc::pluck('id');
    //    $n = array_rand($d->toArray(), round(2*count($d)/3));
    //    foreach($n as $it){
    //        DiemLayNuoc::where('id', $d[$it])->update([
    //            'hien_thi_tren_map'=>false
    //        ]);       
    //    };
    DiemLayNuoc::where('quan_huyen_id', 3)->delete();
       return response('Thành công', 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\XuLyViPham  $xuLyViPham
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = XuLyViPham::with('files', 'nhomHanhVis')->where('id', $id)->first();
        return response($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\XuLyViPham  $xuLyViPham
     * @return \Illuminate\Http\Response
     */
    public function edit(XuLyViPham $xuLyViPham)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\XuLyViPham  $xuLyViPham
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'thoi_gian'  => 'required',
            'noi_dung_hanh_vi'  => 'required',
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
        $data['thoi_gian'] =  Carbon::parse($data['thoi_gian'])->timezone('Asia/Ho_Chi_Minh');
        if (count($data['thoi_gian_tam_dinh_chi']) == 2) {
            $data['ngay_tam_dinh_chi'] =  Carbon::parse($data['thoi_gian_tam_dinh_chi'][0])->timezone('Asia/Ho_Chi_Minh');
            $data['ngay_phuc_hoi'] =  Carbon::parse($data['thoi_gian_tam_dinh_chi'][1])->timezone('Asia/Ho_Chi_Minh');
        } else {
            $data['ngay_tam_dinh_chi'] =  null;
            $data['ngay_phuc_hoi'] =  null;
        }
        try {
            $viPham = XuLyViPham::where('id', $id)->first()->update([
                'tinh_thanh_id' => $data['tinh_thanh_id'],
                'thoi_gian' => $data['thoi_gian'],
                'doi_tuong_vi_pham' => $data['doi_tuong_vi_pham'],
                'noi_dung_hanh_vi' => $data['noi_dung_hanh_vi'],
                'dinh_chi' => $data['dinh_chi'],
                'tam_dinh_chi' => $data['tam_dinh_chi'],
                'phat_tien' => $data['phat_tien'],
                'xu_ly_khac' => $data['xu_ly_khac'],
                'canh_cao' => $data['canh_cao'],
                'ngay_tam_dinh_chi' => $data['ngay_tam_dinh_chi'],
                'ngay_phuc_hoi' => $data['ngay_phuc_hoi'],
                'toa_nha_id' => $data['toa_nha_id']
            ]);
            ViPhamNhomHanhVi::where('vi_pham_id', $id)->delete();
            if (count($data['nhom_hanh_vi_id']) > 0) {
                foreach ($data['nhom_hanh_vi_id'] as $it) {
                    ViPhamNhomHanhVi::create([
                        'vi_pham_id' => $id,
                        'nhom_hanh_vi_id' => $it
                    ]);
                }
            }
            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e,
                'message' => 'Không thể cập nhật',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\XuLyViPham  $xuLyViPham
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            XuLyViPham::find($id)->delete();
            return response()->json([
                'message' => 'Xóa thành công',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e,
                'message' => 'Không thể xóa',
            ], 500);
        }
    }
}
