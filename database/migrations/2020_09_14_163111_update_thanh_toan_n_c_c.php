<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateThanhToanNCC extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('don_hang_thanh_toan_nha_cung_caps', function (Blueprint $table) {
            $table->integer('don_tra_hang_id')->nullable();
            $table->foreign('don_tra_hang_id')->references('id')->on('tra_hang_nha_cung_caps')->onDelete('cascade');
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
