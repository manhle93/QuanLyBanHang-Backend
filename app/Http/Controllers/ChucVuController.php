<?php

namespace App\Http\Controllers;

use App\ChucVu;
use Illuminate\Http\Request;

class ChucVuController extends Controller
{
    public function index(){
        $chucVu = ChucVu::query()->orderBy('level', "DESC")->select('id','ten')->get();
        return response($chucVu, 200);
    }
}
