<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DonDatHangPhieuNhapKho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phieu_nhap_khos', function (Blueprint $table) {
            $table->integer('don_dat_hang_id')->nullable();
            $table->foreign('don_dat_hang_id')->references('id')->on('don_dat_hangs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
