<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDonDatHang2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('don_dat_hangs', function (Blueprint $table) {
            $table->string('thanh_toan')->nullable();
        });

        Schema::table('nop_tiens', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
        Schema::table('nop_tiens', function (Blueprint $table) {
            $table->string('trang_thai')->nullable();
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
