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
    Route::apiResource('danh-muc', 'DanhMucController');
    Route::get('danh-muc-con', 'DanhMucController@getDanhMucCon');

    Route::post('addcompany', 'SysAdminController@addCompany');
    Route::post('config/add', 'AdminController@addConfig');
    Route::get('config', 'AdminController@index');
    Route::post('config/{id}/edit', 'AdminController@edit');
    Route::delete('config/{id}/delete', 'AdminController@delete');
    Route::get('infor', 'AdminController@getInfor');
    Route::post('infor', 'AdminController@editInfor');
    Route::post('changepass', 'AdminController@updatePassword'); //Đổi mật khẩu
    Route::post('avatarupload', 'AdminController@uploadAvatar'); //upload anh avatar
    Route::get('mobile/me', 'MobileController@me');
    Route::get('getData', 'ReportController@getData');
});
Route::group(['prefix' => 'system'], function () {
    Route::apiResource('roles', 'System\RoleController');
    Route::apiResource('companies', 'System\CompanyController');
    Route::get('allcompany', 'SysAdminController@index');
    Route::get('mobile/me', 'MobileController@me');
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

Route::get('danhmucmobile', 'DanhMucController@getDanhMucMobile');
Route::get('refreshcaptcha', 'AuthController@refreshCaptcha');
Route::get('checkusercaptcha', 'AuthController@checkUser');

// ******************Loading for RBT************************
Route::get('danhmuc', 'DanhMucSanPhamController@getDanhMucSanPham');
Route::get('danhmucmobile', 'DanhMucSanPhamController@danhMucSanPhamMobile');
Route::get('baiviet', 'CaiDatController@getBaiViet');
Route::get('baiviet/{id}', 'CaiDatController@getChiTietBaiViet');

Route::get('idmonngonmoingay', 'CaiDatController@getMonNgonMoiNgay');
Route::get('slider', 'CaiDatController@getSilder');
Route::get('exportsanpham', 'SanPhamController@exportSanPham');
Route::get('exportsanpham', 'SanPhamController@exportSanPham');
Route::get('updatema', 'SanPhamController@updateMaSanPham');
Route::get('sanphambanchaytrangchu', 'SanPhamController@getSanPhamBanChay');
Route::get('sanpham', 'SanPhamController@getSanPham');
Route::get('sanphamgiohang', 'SanPhamController@getSanPhamGioHang');
Route::post('sanphamgiohang', 'SanPhamController@getSanPhamGioHangMobile');
Route::get('sanphamtrangchu/{id}', 'SanPhamController@getSanPhamDetailTrangChu');
Route::get('downloadsanpham', 'SanPhamController@downloadMauSanPham');
Route::post('khachhang', 'KhachHangNhaCungCapController@addKhachHang');
Route::post('loginkhachhang', 'KhachHangNhaCungCapController@loginKhachHang');

// ***********************************************************


Route::group(['middleware' => 'auth'], function () {
    Route::post('danhmuc', 'DanhMucSanPhamController@addDanhMucSanPham');
    Route::put('danhmuc', 'DanhMucSanPhamController@editDanhMucSanPham');
    Route::delete('danhmuc/{id}', 'DanhMucSanPhamController@xoaDanhMuc');
    Route::post('anhdanhmuc', 'DanhMucSanPhamController@uploadAnhDanhMuc');
    
    Route::post('uploadsanpham', 'SanPhamController@importSanPham');
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

    Route::get('donhangconno', 'KhachHangNhaCungCapController@getDonHangConNo');
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
    
    Route::post('doihang/{id}', 'DonDatHangController@doiHang');
    Route::post('trahang/{id}', 'DonDatHangController@traHang');

    Route::post('capnhatkhachhang', 'KhachHangNhaCungCapController@updateThongTinCaNhan');
    Route::get('chitietkhachhang', 'KhachHangNhaCungCapController@getChiTietKhachHang');

    Route::get('tonkho', 'QuanLyKhoController@getHangTonKho');
});

    Route::get('tien', 'DonDatHangController@test');
    Route::get('inhoadon/{id}', 'DonDatHangController@inHoaDon');
    Route::post('khachdathang', 'DonDatHangController@datHang');
    Route::get('inphieuthu/{id}', 'DonDatHangController@inPhieuThu');
    Route::get('inhoadonnhacungcap/{id}', 'DonHangNhaCungCapController@inHoaDon');
    
    Route::get('mobile/showdangky', 'AuthController@showDangKy');
    Route::post('mobile/dangkynhacungcap', 'System\UserController@dangKyNhaCungCap');
    // bao cao he thong 
    Route::get('baocaobanhang', 'BaoCaoController@getBaoCaoBanHang');
    Route::get('baocaodathang', 'BaoCaoController@getBaoCaoDatHang');
    Route::get('baocaokhachhang', 'BaoCaoController@getBaoCaoKhachHang');
    Route::get('baocaonhanvien', 'BaoCaoController@getBaoCaoNhanVien');
    Route::get('baocaotaichinh', 'BaoCaoController@getBaoCaoTaiChinh');
    Route::get('baocaohanghoa', 'BaoCaoController@getBaoCaoHangHoa');
    Route::get('baocaonhacungcap', 'BaoCaoController@getBaoCaoNhaCungCap');
    Route::get('baocaocuoingay', 'BaoCaoController@getBaoCaoCuoiNgay');
    Route::get('downloadbaocaobanhang', 'BaoCaoController@downloadBaoCaoBanHang');
    Route::get('downloadbaocaodathang', 'BaoCaoController@downloadBaoCaoDatHang');
    Route::get('downloadbaocaokhachhang', 'BaoCaoController@downloadBaoCaoKhachHang');
    Route::get('downloadbaocaonhanvien', 'BaoCaoController@downloadBaoCaoNhanVien');
    Route::get('downloadbaocaotaichinh', 'BaoCaoController@downloadBaoCaoTaiChinh');
    Route::get('downloadbaocaohanghoa', 'BaoCaoController@downloadBaoCaoHangHoa');
    Route::get('downloadbaocaonhacungcap', 'BaoCaoController@downloadBaoCaoNhaCungCap');
    Route::get('downloadbaocaocuoingay', 'BaoCaoController@downloadBaoCuoiNgay');
    Route::get('lichsudangnhap', 'AuthController@getLichSuDangNhap');
    Route::get('lichsuhoatdong', 'AuthController@getLichSuHoatDong');


