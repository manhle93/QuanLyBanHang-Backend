<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonHangThanhToanNhaCungCapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('don_hang_thanh_toan_nha_cung_caps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('don_thanh_toan_id')->nullable();
            $table->foreign('don_thanh_toan_id')->references('id')->on('thanh_toan_nha_cung_caps')->onDelete('cascade');
            $table->integer('don_hang_id')->nullable();
            $table->foreign('don_hang_id')->references('id')->on('don_hang_nha_cung_caps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('don_hang_thanh_toan_nha_cung_caps');
    }
}
