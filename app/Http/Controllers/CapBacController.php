<?php

namespace App\Http\Controllers;

use App\CapBac;
use Illuminate\Http\Request;

class CapBacController extends Controller
{
    public function index(){
        $capBac = CapBac::query()->orderBy('level', "ASC")->select('id', 'ten_cap_bac')->get();
        return response($capBac, 200);
    }
}
