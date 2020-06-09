<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Service;
use App\ShiftWork;
use Carbon\Carbon;
use App\WorkPlaceEmployee;
use App\WorkCalendar;
use App\Checking;
use App\Company;
use App\DanhMuc;
use App\Error;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // DanhMuc::create([
        //     'ma' => 'LTB',
        //     'ten' => "Loại thiết bị",
        //     "parent_id" => null,
        //     'trang_thai' => true,
        // ]);
        // DanhMuc::create([
        //     'ma' => 'LCB',
        //     'ten' => "Loại cảm biến",
        //     "parent_id" => null,
        //     'trang_thai' => true,
        // ]);
        // DanhMuc::create([
        //     'ma' => 'LMQ',
        //     'ten' => "Loại máy quay",
        //     "parent_id" => null,
        //     'trang_thai' => true,
        // ]);
        // DanhMuc::create([
        //     'ma' => 'LPTPCCC',
        //     'ten' => "Loại phương tiện PCCC",
        //     "parent_id" => null,
        //     'trang_thai' => true,
        // ]);
        // DanhMuc::create([
        //     'ma' => 'LĐVHT',
        //     'ten' => "Loại đơn vị hỗ trợ",
        //     "parent_id" => null,
        //     'trang_thai' => true,
        // ]);
        $nguoidung = App\Menu::create([
            'name' => 'Người dùng',
            'active' => 'true',
        ]);
        $nguoidung->children()->createMany([
            [
                'name' => "Thông tin tài khoản",
                'active' => true,
            ],
            [
                'name' => "Quản lý người dùng",
                'active' => true,
            ],
            [
                'name' => "Phân quyền",
                'active' => true,
            ]
        ]);

        $quanlykhuvuc = App\Menu::create([
            'name' => 'Quản lý khu vực',
            'active' => 'true',
        ]);
        $quanlykhuvuc->children()->createMany([
            [
                'name' => "Tỉnh thành",
                'active' => true,
            ],
            [
                'name' => "Quận huyện",
                'active' => true,
            ],
        ]);
        $danhmuc = App\Menu::create([
            'name' => 'Danh mục',
            'active' => true,
        ]);
        $danhmuc->children()->create([
            'name' => 'Danh sách danh mục',
            'active' => true,
        ]);
        $qlchung = App\Menu::create([
            'name' => 'Quản lý chung',
            'active' => true,
        ]);
        $qlchung->children()->createMany([
            [
                'name' => 'Quản lý tòa nhà',
                'active' => true
            ],
            [
                'name' => 'Thêm mới tòa nhà',
                'active' => true
            ],
            [
                'name' => 'Thông tin tòa nhà',
                'active' => true
            ],
            [
                'name' => 'Quản lý dân cư',
                'active' => true
            ],
            [
                'name' => 'Quản lý cảm biến',
                'active' => true
            ],
            [
                'name' => 'Quản lý thiết bị',
                'active' => true
            ],
            [
                'name' => 'Quản lý điểm cháy',
                'active' => true
            ],
            [
                'name' => 'Thông tin điểm cháy',
                'active' => true
            ],
            [
                'name' => 'Quản lý thiết bị quay',
                'active' => true
            ],
            [
                'name' => 'Thêm mới thiết bị quay',
                'active' => true
            ],
            [
                'name' => 'Thông tin thiết bị quay',
                'active' => true
            ]
        ]);
        $qlpccc = App\Menu::create([
            'name' => 'Quản lý PCCC',
            'active' => true,
        ]);
        $qlpccc->children()->createMany([
            [
                'name' => 'Đơn vị PCCC',
                'active' => true
            ],
            [
                'name' => 'Thêm mới đơn vị PCCC',
                'active' => true
            ],
            [
                'name' => 'Thông tin đơn vị PCCC',
                'active' => true
            ],
            [
                'name' => 'Phương tiện PCCC',
                'active' => true
            ],
            [
                'name' => 'Điểm lấy nước',
                'active' => true
            ],
            [
                'name' => 'Thêm mới điểm lấy nước',
                'active' => true
            ],
            [
                'name' => 'Thông tin điểm lấy nước',
                'active' => true
            ],
            [
                'name' => 'Đơn vị hỗ trợ',
                'active' => true
            ],
            [
                'name' => 'Thêm mới đơn vị hỗ trợ',
                'active' => true
            ],
            [
                'name' => 'Thông tin đơn vị hỗ trợ',
                'active' => true
            ],
        ]);

        $baocao = App\Menu::create(
            ['name' => 'Báo cáo', 'active' => true]
        );
        $baocao->children()->create(['name' => 'Danh sách báo cáo', 'active' => true]);
    }
}
