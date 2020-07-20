<?php

namespace App\Http\Controllers;

use App\HangTonKho;
use App\SanPham;
use Illuminate\Http\Request;

class KiemKhoController extends Controller
{
    public function getSanPhamTonKho(){
        $tonKho = HangTonKho::pluck('san_pham_id')->toArray();
        $sanPham = SanPham::get();
        foreach($sanPham as $item){
            if(in_array($item->id, $tonKho)){
                $item['ton_kho'] = HangTonKho::where('san_pham_id', $item->id)->first()->so_luong;
            }else $item['ton_kho'] = 0;
        }
        return $sanPham;
    }
}
