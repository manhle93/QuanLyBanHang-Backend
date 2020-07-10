<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHangTonKhosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hang_ton_khos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('san_pham_id')->nullable();
            $table->foreign('san_pham_id')->references('id')->on('san_phams')->onDelete('cascade');
            $table->double('so_luong')->nullable();
            $table->integer('kho_id')->nullable();
            $table->foreign('kho_id')->references('id')->on('khos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hang_ton_khos');
    }
}
