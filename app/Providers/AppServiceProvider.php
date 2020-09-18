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
        \App\User::observe(\App\Observers\UserObserver::class);
        \App\DonDatHang::observe(\App\Observers\DonDatHangObserver::class);
        \App\ThanhToanNhaCungCap::observe(\App\Observers\ThanhToanNhaCungCapObserver::class);
        \App\TraHangNhaCungCap::observe(\App\Observers\TraHangNhaCungCapObserver::class);
        \App\DonHangNhaCungCap::observe(\App\Observers\DonHangNhaCungCapObserver::class);
        \App\NopTien::observe(\App\Observers\NopTienCapObserver::class);
    }

}
