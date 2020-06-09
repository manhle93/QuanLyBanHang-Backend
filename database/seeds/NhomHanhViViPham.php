<?php

use App\NhomHanhViViPham as AppNhomHanhViViPham;
use Illuminate\Database\Seeder;

class NhomHanhViViPham extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppNhomHanhViViPham::create([
            'noi_dung' => 'Hành vi vi phạm trong việc ban hành, phổ biến và tổ chức thực hiện quy định, nội quy về phòng cháy và chữa cháy',
            'level' => 1,
        ]);
        AppNhomHanhViViPham::create([
            'noi_dung' => 'Hành vi vi phạm quy định về kiểm tra an toàn phòng cháy và chữa cháy',
            'level' => 2,
        ]);
        AppNhomHanhViViPham::create([
            'noi_dung' => 'Hành vi vi phạm về hồ sơ quản lý công tác an toàn phòng cháy và chữa cháy',
            'level' => 3,
        ]);
        AppNhomHanhViViPham::create([
            'noi_dung' => 'Hành vi vi phạm quy định về phòng cháy và chữa cháy trong sản xuất kinh doanh chất, hàng nguy hiểm về cháy nổ',
            'level' => 4,
        ]);
    }
}
