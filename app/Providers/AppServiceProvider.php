<?php

namespace App\Providers;

use App\Observers\EmployeeObserver;
use App\Observers\ThietBiObserver;
use App\ThietBi;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\DiemChay::observe(\App\Observers\DiemChayObserver::class);
        \App\DiemLayNuoc::observe(\App\Observers\DiemLayNuocObserver::class);
        \App\ToaNha::observe(\App\Observers\ToaNhaObserver::class);
        \App\User::observe(\App\Observers\UserObserver::class);
        \App\CanBoChienSi::observe(\App\Observers\CanBoChienSiObserver::class);
        \App\PhuongTienPccc::observe(\App\Observers\PhuongTienObserver::class);
        \App\DonViHoTro::observe(\App\Observers\DonViHoTroObserver::class);
        \App\DonViPccc::observe(\App\Observers\DonViPcccObserver::class);
        \App\CamBien::observe(\App\Observers\CamBienObserver::class);
        \App\ThietBi::observe(\App\Observers\ThietBiObserver::class);
        \App\ThietBiQuay::observe(\App\Observers\ThietBiQuayObserver::class);
        \App\KiemTraToaNha::observe(\App\Observers\KiemTraToaNhaObserver::class);
        \App\DanCu::observe(\App\Observers\DanCuToaNhaObserver::class);
        \App\HuanLuyenBoiDuong::observe(\App\Observers\HuanLuyenBoiDuongObserver::class);
        \App\ThamDinhPheDuyet::observe(\App\Observers\ThamDinhPheDuyetObserver::class);
        \App\XuLyViPham::observe(\App\Observers\XuLyViPhamObserver::class);
        \App\CuuHoCuuNan::observe(\App\Observers\CuuHoCuuNanObserver::class);
        \App\LichTruc::observe(\App\Observers\LichTrucObserver::class);
    }

}
