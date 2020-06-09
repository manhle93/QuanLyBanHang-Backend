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


Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('check-company', 'AuthController@checkExistCompanyCode');
    Route::post('setup', 'AuthController@setup');
    Route::get('resend/{user}', 'AuthController@resendVerifyEmail');
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('mobile/login', 'AuthController@loginMobile');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
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
    Route::post('building/diemchay/toanha/{id}','BuildingController@addToaNha');

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

Route::get('danhmucmobile', 'DanhMucController@getDanhMucMobile');

});

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
