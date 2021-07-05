<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNhapKhoTamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nhap_kho_tam', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ma')->nullable();
            $table->string('ten')->nullable();
            $table->string('ghi_chu')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('don_hang_id')->nullable();
            $table->integer('kho_id')->nullable();
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
        Schema::dropIfExists('nhap_kho_tam');
    }
}
