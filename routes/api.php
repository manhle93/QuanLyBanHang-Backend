<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('check-company', 'AuthController@checkExistCompanyCode');
    Route::post('setup', 'AuthController@setup');
    Route::get('resend/{user}', 'AuthController@resendVerifyEmail');
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('mobile/login', 'AuthController@loginMobile');
    Route::post('logout', 'AuthController@logout');
    Route::get('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('getPolygon', 'AdminController@getPolygon');
    Route::get('thiet-bi/cam-bien/{id}', 'ThietBiController@camBien');
    Route::get('thiet-bi/toa-nha', 'ThietBiController@toaNha');
    Route::get('thiet-bi/excel', 'ThietBiController@excel');
    Route::apiResource('cam-bien', 'CamBienController');
    Route::apiResource('thiet-bi', 'ThietBiController');
    Route::apiResource('danh-muc', 'DanhMucController');
    Route::get('phuong-tien-pccc/don-vi-pccc', 'PhuongTienPcccController@getDonViPccc');
    Route::get('phuong-tien-pccc/vi-tri/{imei}', 'PhuongTienPcccController@getViTriPhuongTienPccc');
    Route::apiResource('phuong-tien-pccc', 'PhuongTienPcccController');
    Route::get('danh-muc-con', 'DanhMucController@getDanhMucCon');
    Route::post('thiet-bi/them-cam-bien/{id}', 'ThietBiController@themCamBien');

    Route::post('addcompany', 'SysAdminController@addCompany');
    Route::post('config/add', 'AdminController@addConfig');
    Route::get('config', 'AdminController@index');
    Route::post('config/{id}/edit', 'AdminController@edit');
    Route::delete('config/{id}/delete', 'AdminController@delete');
    Route::get('infor', 'AdminController@getInfor');
    Route::post('infor', 'AdminController@editInfor');
    Route::post('changepass', 'AdminController@updatePassword'); //Đổi mật khẩu
    Route::post('avatarupload', 'AdminController@uploadAvatar'); //upload anh avatar

    Route::post('donvipcc/avatarupload/{id}', 'DonViController@uploadAvatar'); //upload anh avatar

    Route::post('donvipccc/add', 'DonViController@store');
    Route::get('donvipccc/all', 'DonViController@getAll');
    Route::get('donvipccc', 'DonViController@index');
    Route::get('donvipccc/list', 'DonViController@list');
    Route::get('donvipccc/{id}', 'DonViController@show');
    Route::put('donvipccc/{id}/edit', 'DonViController@edit');
    Route::delete('donvipccc/{id}/delete', 'DonViController@delete');
    Route::get('donvipccc/list/{data}', 'DonViController@search');

    Route::get('dancu', 'DanCuController@getDanCu');
    Route::post('dancu/add', 'DanCuController@addDanCu');
    Route::put('dancu/{id}', 'DanCuController@editDanCu');
    Route::delete('dancu/delete/{id}', 'DanCuController@deleteDanCu');

    Route::get('allbuilding/', 'BuildingController@listAll');

    Route::get('building', 'BuildingController@list');
    Route::get('building/thiet-bi', 'BuildingController@getThietBi');
    Route::get('building/{id}', 'BuildingController@show');
    Route::post('building/add', 'BuildingController@store');
    Route::put('building/{id}/edit', 'BuildingController@edit');
    Route::delete('building/{id}/delete', 'BuildingController@delete');
    Route::get('building/{id}/file', 'BuildingController@getDownload');
    Route::post('building/diemchay/toanha/{id}', 'BuildingController@addToaNha');

    Route::get('kiemtratoanha', 'KiemTraToaNhaController@index');
    Route::get('trangthaikiemtra', 'KiemTraToaNhaController@getTrangThaiKiemTra');
    Route::delete('kiemtratoanha/{id}', 'KiemTraToaNhaController@delete');
    Route::post('kiemtra/add', 'KiemTraToaNhaController@store');
    Route::get('kiemtra/{id}', 'KiemTraToaNhaController@show');
    Route::put('kiemtra/{id}/edit', 'KiemTraToaNhaController@edit');

    Route::get('diemchay/index', 'DiemChayController@getData');
    Route::post('diemchay/goidien', 'DiemChayController@goiDien');
    Route::get('diemchay/list', 'DiemChayController@dataPaginate');
    Route::get('diemchay', 'DiemChayController@getData');
    Route::get('diemchay/don-vi-ho-tro/{id}', 'DiemChayController@getDonViHoTro');
    Route::get('diemchay/phuong-tien-pccc/{id}', 'DiemChayController@getPhuongTienPccc');
    Route::post('diemchay/don-vi-ho-tro/{diem_chay}', 'DiemChayController@donViHoTro');
    Route::post('diemchay/phuong-tien-pccc/{diem_chay}', 'DiemChayController@phuongTienPccc');
    Route::post('callphone', 'DiemChayController@call');
    Route::get('diemchay/{id}', 'DiemChayController@show');
    Route::post('diemchay/add', 'DiemChayController@store');
    Route::put('diemchay/{id}/edit', 'DiemChayController@edit');
    Route::delete('diemchay/{id}/delete', 'DiemChayController@delete');
    Route::get('diemchay/export/excel', 'DiemChayController@export');
    Route::get('diemchay/export/excel2', 'DiemChayController@excel');

    Route::get('thietbiquay', 'ThietBiQuayController@getThietBiQuay');
    Route::delete('thietbiquay/delete/{id}', 'ThietBiQuayController@deleteThietBiQuay');
    Route::get('thietbiquay/{id}', 'ThietBiQuayController@show');
    Route::post('thietbiquay/add', 'ThietBiQuayController@store');
    Route::put('thietbiquay/{id}/edit', 'ThietBiQuayController@edit');
    Route::post('upload', 'BuildingController@upload');
    Route::delete('file/{id}/delete', 'BuildingController@deleteFile');

    Route::get('getData', 'ReportController@getData');
});
Route::group(['prefix' => 'system'], function () {
    Route::apiResource('roles', 'System\RoleController');
    Route::apiResource('companies', 'System\CompanyController');
    Route::get('allcompany', 'SysAdminController@index');
});

Route::group(['middleware' => 'api', 'prefix' => 'mobile'], function () {
    Route::get('diemchay', 'DiemChayController@index');
    Route::get('building', 'BuildingController@index');
    Route::get('employee', 'AuthController@getEmployeeInfo');
    Route::post('/', 'AuthController@getTokenByCheckingCode');
});

Route::post('user/add', 'System\UserController@store');
Route::post('avatarupload/{id}', 'System\UserController@uploadAvatar');
Route::get('user', 'System\UserController@index');
Route::put('user/{id}/edit', 'System\UserController@edit');
Route::delete('user/{id}/delete', 'System\UserController@delete');
Route::get('khachhang/{id}', 'System\UserController@getKhachHang');
Route::get('thongtindathang', 'KhachHangNhaCungCapController@getThongTinDatHang');
Route::post('updatenhanvien', 'System\UserController@updateNhanVien');

Route::post('tinhthanh', 'TinhThanhController@store');
Route::get('tinhthanh/don-vi-pccc/{tinh_thanh}', 'TinhThanhController@getDonViPccc');
Route::get('tinhthanh', 'TinhThanhController@index');
Route::put('tinhthanh/{id}', 'TinhThanhController@edit');
Route::delete('tinhthanh/{id}', 'TinhThanhController@delete');

Route::post('quanhuyen', 'QuanHuyenController@store');
Route::get('quanhuyen', 'QuanHuyenController@index');
Route::get('quanhuyentheotinh', 'QuanHuyenController@getQuanHuyenTheoTinh');
Route::put('quanhuyen/{id}', 'QuanHuyenController@edit');
Route::delete('quanhuyen/{id}', 'QuanHuyenController@delete');

Route::post('diemlaynuoc', 'DiemLayNuocController@store');
Route::get('diemlaynuoc', 'DiemLayNuocController@index');
Route::put('diemlaynuoc/{id}', 'DiemLayNuocController@edit');
Route::delete('diemlaynuoc/{id}', 'DiemLayNuocController@delete');
Route::get('diemlaynuoc/{id}', 'DiemLayNuocController@show');

Route::post('donvihotro', 'DonViHoTroController@store');
Route::get('donvihotro', 'DonViHoTroController@index');
Route::put('donvihotro/{id}', 'DonViHoTroController@edit');
Route::delete('donvihotro/{id}', 'DonViHoTroController@delete');
Route::get('donvihotro/{id}', 'DonViHoTroController@show');
Route::get('loaidonvihotro', 'DonViHoTroController@getLoaiDonVi');
Route::get('testcron', 'DonViHoTroController@callDiemchay');
Route::get('getAddressByLatLong', 'TinhThanhController@getAddressByLatLong');
Route::get('getLatLongByAddressText', 'TinhThanhController@getLatLongByAddressText');

Route::get('role-menu/role', 'RoleController@getRoleMenu');
Route::get('role-menu/menu', 'RoleController@getMenuRole');
Route::post('role-menu/{role}', 'RoleController@addMenuToRole');
Route::get('role-menu/list-menu', 'RoleController@getMenus');

//Route::get('getXe/{id}', 'PhuongTienPcccController@getXe');

Route::post('reset-password', 'ResetPasswordController@sendMail');
Route::put('create-password', 'ResetPasswordController@reset');

Route::get('getXe/{id}', 'PhuongTienPcccController@getXe');
Route::get('search', 'DonViController@search');
Route::get('search1', 'DanCuController@search');




Route::group(['middleware' => 'auth'], function () {

    Route::get('taodiemchay', 'DonViHoTroController@create');
    Route::get('toanha/{id}', 'BuildingController@getToaNhaTheoTinhThanh');

    Route::get('baocaothietbi/{id}', 'BaoCaoController@excelThietBiTinhThanh');
    Route::get('sothietbitinhthanh', 'BaoCaoController@getThietBiTheoTinh');
    Route::get('trangthaithietbi', 'BaoCaoController@getTrangThaiThietBi');
    Route::get('databieudothietbi', 'BaoCaoController@getDataBieuDoThietBi');
    Route::get('databieudovuchay', 'BaoCaoController@getDataBieuDoVuChay');
    Route::get('databieudothiethai', 'BaoCaoController@getDataBieuDoThietHai');
    Route::get('polygoltinhthanh', 'BaoCaoController@getPolygon');

    Route::get('baocaodiemchay/{id}', 'BaoCaoController@excelDiemChayTinhThanh');

    Route::get('baocaosochiensi/{id}', 'BaoCaoController@excelCanBoThamGiaChuaChay');

    Route::get('sothietbionline', 'BaoCaoController@getSoThietBiOnlineOffline');
    Route::get('danhsachthongbao', 'BaoCaoController@getThongBao');
    Route::get('docthongbao', 'BaoCaoController@docThongBao');
    Route::get('danhsachvuchay', 'BaoCaoController@getDiemChay');
    Route::get('danhsachdonvi', 'BaoCaoController@getDonVi');

    Route::get('danhsachcongtrinh', 'BaoCaoController@getToaNha');
    Route::get('diemdangchay', 'BaoCaoController@getDiemDangChay');

    Route::post('uploadthietbi', 'ImportExcelController@importThietBi');

    Route::get('qltinhthanh', 'QuanLyChungController@getTinhThanh');
    Route::get('danhsachxe', 'QuanLyChungController@getPT');
    Route::get('dsdonvi', 'QuanLyChungController@getDV');
    Route::get('chitiettinhthanh', 'QuanLyChungController@getChiTietTinhThanh');

    Route::post('themcanbochiensi', 'CanBoChienSiController@add');
    Route::get('danhsachcanbochiensi', 'CanBoChienSiController@index');
    Route::put('editcanbochiensi/{id}', 'CanBoChienSiController@update');
    Route::delete('xoacanbochiensi/{id}', 'CanBoChienSiController@destroy');
    Route::get('capbac', 'CapBacController@index');
    Route::get('chucvu', 'ChucVuController@index');


    Route::post('uploadtoanha', 'ImportExcelController@importToaNha');
    Route::post('uploadnhansu', 'ImportExcelController@importNhanSu');
    Route::post('uploadphuongtien', 'ImportExcelController@importPhuongTien');
    Route::post('uploaddonvihotro', 'ImportExcelController@importDonViHoTro');
    Route::post('uploaddiemlaynuoc', 'ImportExcelController@importDiemLayNuoc');

    Route::post('phuonganthuctapchuachay', 'ThucTapPhuongAnChuaChayController@create');
    Route::get('phuonganthuctapchuachay', 'ThucTapPhuongAnChuaChayController@index');
    Route::delete('phuonganthuctapchuachay/{id}', 'ThucTapPhuongAnChuaChayController@delete');
    Route::get('phuonganthuctapchuachay/{id}', 'ThucTapPhuongAnChuaChayController@show');
    Route::put('phuonganthuctapchuachay/{id}', 'ThucTapPhuongAnChuaChayController@update');

    Route::post('huanluyenboiduong', 'HuanLuyenBoiDuongController@create');
    Route::get('huanluyenboiduong', 'HuanLuyenBoiDuongController@index');
    Route::get('huanluyenboiduong/{id}', 'HuanLuyenBoiDuongController@show');
    Route::put('huanluyenboiduong/{id}', 'HuanLuyenBoiDuongController@update');
    Route::delete('huanluyenboiduong/{id}', 'HuanLuyenBoiDuongController@delete');


    Route::post('thamdinhpheduyet', 'ThamDinhPheDuyetController@add');
    Route::get('thamdinhpheduyet', 'ThamDinhPheDuyetController@index');
    Route::get('thamdinhpheduyet/{id}', 'ThamDinhPheDuyetController@show');
    Route::put('thamdinhpheduyet/{id}', 'ThamDinhPheDuyetController@update');
    Route::delete('thamdinhpheduyet/{id}', 'ThamDinhPheDuyetController@delete');

    Route::post('thaydoipccc', 'ToaNhaThayDoiPcccController@store');
    Route::delete('thaydoipccc/{id}', 'ToaNhaThayDoiPcccController@destroy');
    Route::put('thaydoipccc', 'ToaNhaThayDoiPcccController@edit');

    Route::get('nhomhanhvi', 'XuLyViPhamController@getNhomHanhVi');
    Route::post('xulyvipham', 'XuLyViPhamController@create');
    Route::get('xulyvipham', 'XuLyViPhamController@index');
    Route::get('xulyvipham/{id}', 'XuLyViPhamController@show');
    Route::put('xulyvipham/{id}', 'XuLyViPhamController@update');
    Route::delete('xulyvipham/{id}', 'XuLyViPhamController@destroy');

    Route::get('lichsudangnhap', 'AuthController@getLichSuDangNhap');
    Route::get('lichsuhoatdong', 'AuthController@getLichSuHoatDong');

    // Route::get('antrunuoc', 'XuLyViPhamController@anTruNuoc');
    Route::get('getdatapolygon', 'ReportController@getDataPolygon');

    Route::post('cuunancuuho', 'CuuHoCuuNanController@create');
    Route::get('cuunancuuho', 'CuuHoCuuNanController@list');
    Route::get('cuunancuuho/{id}', 'CuuHoCuuNanController@show');
    Route::put('cuunancuuho/{id}', 'CuuHoCuuNanController@update');
    Route::delete('cuunancuuho/{id}', 'CuuHoCuuNanController@delete');

    Route::post('lichtruc', 'LichTrucController@add');
    Route::get('lichtruc', 'LichTrucController@index');
    Route::get('lichtruc/{id}', 'LichTrucController@show');
    Route::put('lichtruc/{id}', 'LichTrucController@update');
    Route::delete('lichtruc/{id}', 'LichTrucController@delete');
    Route::get('thongbaomobile', 'BaoCaoController@getThongmobile');


    Route::post('pccccoso', 'PhuongTienToaNhaController@addPcccCoSo');
    Route::put('pccccoso', 'PhuongTienToaNhaController@updatePcccCoSo');
    Route::delete('pccccoso/{id}', 'PhuongTienToaNhaController@xoaPcccCoSo');

    Route::post('phuongtientoanha', 'PhuongTienToaNhaController@addPhuongtien');
    Route::put('phuongtientoanha', 'PhuongTienToaNhaController@updatePhuongTien');
    Route::delete('phuongtientoanha/{id}', 'PhuongTienToaNhaController@xoaPhuongTien');
});
Route::get('danhmucmobile', 'DanhMucController@getDanhMucMobile');
Route::post('baochay', 'DiemChayController@baoChay');
Route::get('refreshcaptcha', 'AuthController@refreshCaptcha');
Route::get('checkusercaptcha', 'AuthController@checkUser');

Route::get('downloadmautoanha', 'ImportExcelController@downloadMauToaNha');
Route::get('downloadmaunhansu', 'ImportExcelController@downloadMauNhanSu');
Route::get('downloadmauphuongtien', 'ImportExcelController@downloadMauPhuongTien');
Route::get('downloadmaudonvihotro', 'ImportExcelController@downloadMauDonViHoTro');
Route::get('downloadmaudiemlaynuoc', 'ImportExcelController@downloadMauDiemLayNuoc');
Route::get('downloadmauthietbi', 'ImportExcelController@downloadMauThietBi');


Route::get('banthongbao', 'BaoCaoController@senNotify');

Route::get('xuatfileword/{id}', 'BuildingController@table');
Route::get('in/{id}', 'BuildingController@table');




Route::group(['middleware' => 'auth'], function () {
    Route::post('danhmuc', 'DanhMucSanPhamController@addDanhMucSanPham');
    Route::put('danhmuc', 'DanhMucSanPhamController@editDanhMucSanPham');
    Route::delete('danhmuc/{id}', 'DanhMucSanPhamController@xoaDanhMuc');
    Route::post('anhdanhmuc', 'DanhMucSanPhamController@uploadAnhDanhMuc');

    Route::post('uploadanhsanpham', 'SanPhamController@upload');
    Route::post('sanpham', 'SanPhamController@addSanPham');
    Route::delete('sanpham/{id}', 'SanPhamController@xoaSanPham');
    Route::get('sanpham/{id}', 'SanPhamController@getSanPhamDetail');
    Route::post('uploadedit/{id}', 'SanPhamController@uploadEdit');
    Route::delete('xoahinhanh', 'SanPhamController@xoaAnhSanPham');
    Route::put('sanpham/{id}', 'SanPhamController@editSanPham');

    Route::post('trahangnhacungcap', 'DonHangNhaCungCapController@traHangNhaCungCap');
    Route::delete('trahangnhacungcap/{id}', 'DonHangNhaCungCapController@xoaDonTrahang');
    Route::put('trahangnhacungcap/{id}', 'DonHangNhaCungCapController@updateDonTraHang');
    Route::get('trahangnhacungcap', 'DonHangNhaCungCapController@getDonTraHang');
    Route::post('themdonhang', 'DonHangNhaCungCapController@addDonHang');
    Route::get('danhsachdonhang', 'DonHangNhaCungCapController@getDonHang');
    Route::get('donhang/{id}', 'DonHangNhaCungCapController@getChiTietDonHang');
    Route::put('donhang/{id}', 'DonHangNhaCungCapController@update');
    Route::put('duyetdon/{id}', 'DonHangNhaCungCapController@duyetDon');
    Route::put('huydon/{id}', 'DonHangNhaCungCapController@huyDon');
    Route::delete('donhang/{id}', 'DonHangNhaCungCapController@xoaDon');
    Route::post('nhapkho/{id}', 'DonHangNhaCungCapController@nhapKho');
    Route::post('nhapkhongoai', 'QuanLyKhoController@addNhapKhoNgoai');

    Route::get('donhangnhacungcapnhapkho/{id}', 'DonHangNhaCungCapController@getDonHangNhaCCC');
    Route::put('thanhtoandonhangnhacungcap/{id}', 'DonHangNhaCungCapController@updateDonThanhToanNCC');
    Route::delete('thanhtoandonhangnhacungcap/{id}', 'DonHangNhaCungCapController@xoaDonThanhToanNCC');
    Route::post('thanhtoandonhangnhacungcap', 'DonHangNhaCungCapController@addDonThanhToanNCC');
    Route::get('thanhtoandonhangnhacungcap', 'DonHangNhaCungCapController@getLichSuThanhToanNCC');
    Route::get('phieunhap', 'QuanLyKhoController@getPhieuNhap');

    Route::get('theodoicongno', 'KhachHangNhaCungCapController@theoDoiCongNo');

    Route::get('thuonghieu', 'ThuongHieuController@getThuongHieu');
    Route::post('addthuonghieu', 'ThuongHieuController@addThuongHieu');
    Route::put('thuonghieu/{id}', 'ThuongHieuController@editThuongHieu');
    Route::delete('thuonghieu/{id}', 'ThuongHieuController@xoaThuongHieu');

    Route::get('kho', 'KhoController@getKho');
    Route::post('kho', 'KhoController@addKho');
    Route::put('kho/{id}', 'KhoController@editKho');
    Route::delete('kho/{id}', 'KhoController@xoaKho');

    Route::post('khachhuy/{id}', 'DonDatHangController@khachHuyDon');
    Route::get('khachhang', 'KhachHangNhaCungCapController@getKhachHang');
    Route::put('khachhang/{id}', 'KhachHangNhaCungCapController@editKhachHang');
    Route::delete('khachhang/{id}', 'KhachHangNhaCungCapController@xoaKhachHang');
    Route::get('profilekhachhang', 'KhachHangNhaCungCapController@thongTinCaNhanKhachHang');
    Route::get('phieuthu', 'DonDatHangController@getPhieuThu');
    Route::put('phieuthu/{id}', 'DonDatHangController@updatePhieuThu');
    Route::post('phieuthu', 'DonDatHangController@addPhieuThu');
    Route::delete('phieuthu/{id}', 'DonDatHangController@xoaPhieuThu');

    Route::get('donhangmobile', 'KhachHangNhaCungCapController@getDonHangMobile');
    Route::get('giaodichmobile', 'KhachHangNhaCungCapController@getGiaoDichMobile');


    Route::get('nhaccungcap', 'KhachHangNhaCungCapController@getNhaCungCap');
    Route::post('nhaccungcap', 'KhachHangNhaCungCapController@addNhaCungCap');
    Route::put('nhaccungcap/{id}', 'KhachHangNhaCungCapController@editNhaCungCap');
    Route::delete('nhaccungcap/{id}', 'KhachHangNhaCungCapController@xoaNhaCungCap');
    Route::post('noptien', 'KhachHangNhaCungCapController@nopTien');
    Route::get('noptien', 'KhachHangNhaCungCapController@lichSuNopTien');
    Route::post('hoantac/{id}', 'KhachHangNhaCungCapController@hoanTac');

    Route::get('banggia', 'BangGiaController@getBangGia');
    Route::post('banggia', 'BangGiaController@addBangGia');
    Route::put('banggia/{id}', 'BangGiaController@editBangGia');
    Route::delete('banggia/{id}', 'BangGiaController@xoaBangGia');
    Route::post('banggia/{id}', 'BangGiaController@saoChep');

    Route::post('banggiasanpham/{id}', 'BangGiaController@addSanPhamBangGia');
    Route::get('banggiasanpham/{id}', 'BangGiaController@getSanPhamBangGia');

    Route::get('sanphambanggia', 'BangGiaController@getSanPham');

    Route::post('thembaogia', 'BaoGiaController@addBaoGia');
    Route::get('baogia', 'BaoGiaController@getBaoGia');
    Route::get('baogia/{id}', 'BaoGiaController@getChiTietBaoGia');
    Route::put('baogia/{id}', 'BaoGiaController@duyetBaoGia');
    Route::put('giabansanpham', 'BaoGiaController@capNhatGiaBan');
    Route::get('sanphamnhacungcap', 'BaoGiaController@getSanPhamBaoGiaNhaCungCap');

    // Route::get('banggiasanpham/{id}', 'BangGiaController@getBangGiaSanPham');

    Route::post('dondathang', 'DonDatHangController@addDonDatHang');
    Route::get('dondathang', 'DonDatHangController@getDonHang');
    Route::delete('dondathang/{id}', 'DonDatHangController@xoaDonHang');
    Route::get('dondathang/{id}', 'DonDatHangController@getChiTietDonDatHang');
    Route::put('dondathang/{id}', 'DonDatHangController@updateDonDatHang');
    Route::put('huydondathang/{id}', 'DonDatHangController@huyDon');
    Route::put('chuyenhoadon/{id}', 'DonDatHangController@chuyenHoaDon');

    Route::post('thanhtoanboxung', 'DonDatHangController@thanhToanBoXung');
    Route::get('shipper', 'System\UserController@getShipper');
    Route::get('doitrahang', 'DonDatHangController@getDonDoiTra');
    Route::get('tonkhodattruoc/{id}', 'DonDatHangController@getTonKhoDatTruoc');
    Route::get('sanphamtonkho', 'KiemKhoController@getSanPhamTonKho');
    Route::post('kiemkho', 'KiemKhoController@addKiemKe');
    Route::get('kiemkho', 'KiemKhoController@getKiemKho');
    Route::get('kiemkho/{id}', 'KiemKhoController@getChiTietKiemKho');
    Route::delete('kiemkho/{id}', 'KiemKhoController@xoaKiemKho');
    Route::put('kiemkho/{id}', 'KiemKhoController@kiemKho');
    Route::put('duyetkiemkho/{id}', 'KiemKhoController@duyetKiemKho');
    Route::put('huykiemkho/{id}', 'KiemKhoController@huyKiemKho');
    Route::get('nhanvien', 'KiemKhoController@getNhanVien');

    Route::post('xuathuy', 'XuatHuyController@addXuaHuy');
    Route::get('xuathuy', 'XuatHuyController@getXuatHuy');
    Route::put('xuathuy/{id}', 'XuatHuyController@getChiTietXuatHuy');

    Route::get('sanphambanchay', 'BieuDoController@getSanPhamBanChay');
    Route::get('doanhthu', 'BieuDoController@getDoanhThu');
    Route::get('dashboard', 'BieuDoController@getThongTinDashBoard');


    Route::get('mobile/sanpham', 'MobileController@getSanPham');
    Route::get('mobile/dondathang', 'MobileController@getDonDatHang');
    Route::get('mobile/dondathang/{id}', 'MobileController@getChiTietDonHang');
    Route::get('mobile/baogia', 'MobileController@getBaoGia');
    Route::get('mobile/baogia/{id}', 'MobileController@getChiTietBaoGia');
    Route::get('mobile/me', 'MobileController@me');


    Route::post('voucher', 'VoucherController@addVoucher');
    Route::get('voucher', 'VoucherController@getVoucher');
    Route::put('voucher/{id}', 'VoucherController@updateVoucher');
    Route::delete('voucher/{id}', 'VoucherController@xoaVoucher');

    Route::post('diemthuong', 'DiemThuongController@addDiemThuong');
    Route::get('diemthuong', 'DiemThuongController@getCauHinhDiemthuong');
    Route::put('diemthuong/{id}', 'DiemThuongController@updateCauHinhDiemthuong');
    Route::delete('diemthuong/{id}', 'DiemThuongController@xoaCauHinh');
    Route::post('avatarkhachhang', 'KhachHangNhaCungCapController@uploadAvatar');
    Route::post('changepasskhachhang', 'KhachHangNhaCungCapController@updatePassword');

    Route::post('uploadslider', 'CaiDatController@uploadAnh');
    Route::post('slider', 'CaiDatController@addSilder');
    Route::put('slider', 'CaiDatController@updateSlider');
    Route::delete('slider/{id}', 'CaiDatController@xoaSilder');

    Route::post('monngonmoingay', 'CaiDatController@addMonNgonMoiNgay');
    Route::post('baiviet', 'CaiDatController@addBaiViet');
    Route::put('baiviet/{id}', 'CaiDatController@editBaiViet');
    Route::delete('baiviet/{id}', 'CaiDatController@xoaBaiViet');
});
Route::get('baiviet', 'CaiDatController@getBaiViet');
Route::get('baiviet/{id}', 'CaiDatController@getChiTietBaiViet');

Route::get('idmonngonmoingay', 'CaiDatController@getMonNgonMoiNgay');
Route::get('slider', 'CaiDatController@getSilder');

Route::get('danhmuc', 'DanhMucSanPhamController@getDanhMucSanPham');
Route::get('danhmucmobile', 'DanhMucSanPhamController@danhMucSanPhamMobile');

Route::post('khachhang', 'KhachHangNhaCungCapController@addKhachHang');
Route::get('chitietkhachhang', 'KhachHangNhaCungCapController@getChiTietKhachHang');
Route::post('khachdathang', 'DonDatHangController@datHang');

Route::get('sanphambanchaytrangchu', 'SanPhamController@getSanPhamBanChay');

Route::post('loginkhachhang', 'KhachHangNhaCungCapController@loginKhachHang');
Route::post('capnhatkhachhang', 'KhachHangNhaCungCapController@updateThongTinCaNhan');
Route::get('sanpham', 'SanPhamController@getSanPham');
Route::get('sanphamgiohang', 'SanPhamController@getSanPhamGioHang');
Route::post('sanphamgiohang', 'SanPhamController@getSanPhamGioHangMobile');
Route::get('sanphamtrangchu/{id}', 'SanPhamController@getSanPhamDetailTrangChu');
Route::get('tonkho', 'QuanLyKhoController@getHangTonKho');
Route::get('inhoadon/{id}', 'DonDatHangController@inHoaDon');
Route::get('inphieuthu/{id}', 'DonDatHangController@inPhieuThu');
Route::get('inhoadonnhacungcap/{id}', 'DonHangNhaCungCapController@inHoaDon');
Route::get('tien', 'DonDatHangController@test');
Route::get('mobile/showdangky', 'AuthController@showDangKy');
Route::post('mobile/dangkynhacungcap', 'System\UserController@dangKyNhaCungCap');
