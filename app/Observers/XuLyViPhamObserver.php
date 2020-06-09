<?php

namespace App\Observers;

use App\XuLyViPham;
use App\LichSuHoatDong;
use Exception;
class XuLyViPhamObserver
{
    /**
     * Handle the xu ly vi pham "created" event.
     *
     * @param  \App\XuLyViPham  $xuLyViPham
     * @return void
     */
    public function created(XuLyViPham $xuLyViPham)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $xuLyViPham->id,
                'type' => 'xu_ly_vi_pham',
                'hanh_dong' => 'created',
                'user_id' => $user->id,
                'noi_dung' => 'Thêm mới xử lý vi phạm'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the xu ly vi pham "updated" event.
     *
     * @param  \App\XuLyViPham  $xuLyViPham
     * @return void
     */
    public function updated(XuLyViPham $xuLyViPham)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $xuLyViPham->id,
                'type' => 'xu_ly_vi_pham',
                'hanh_dong' => 'updated',
                'user_id' => $user->id,
                'noi_dung' => 'Cập nhật xử lý vi phạm'
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the xu ly vi pham "deleted" event.
     *
     * @param  \App\XuLyViPham  $xuLyViPham
     * @return void
     */
    public function deleted(XuLyViPham $xuLyViPham)
    {
        try {
            $user = auth()->user();
            LichSuHoatDong::create([
                'reference_id' => $xuLyViPham->id,
                'type' => 'xu_ly_vi_pham',
                'hanh_dong' => 'deleted',
                'user_id' => $user->id,
                'noi_dung' => 'Xóa xử lý vi phạm. Nội dung: '.$xuLyViPham->noi_dung_hanh_vi.', đối tượng: '.$xuLyViPham->doi_tuong_vi_pham.'. ID tòa nhà: '.$xuLyViPham->toa_nha_id
            ]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Handle the xu ly vi pham "restored" event.
     *
     * @param  \App\XuLyViPham  $xuLyViPham
     * @return void
     */
    public function restored(XuLyViPham $xuLyViPham)
    {
        //
    }

    /**
     * Handle the xu ly vi pham "force deleted" event.
     *
     * @param  \App\XuLyViPham  $xuLyViPham
     * @return void
     */
    public function forceDeleted(XuLyViPham $xuLyViPham)
    {
        //
    }
}
