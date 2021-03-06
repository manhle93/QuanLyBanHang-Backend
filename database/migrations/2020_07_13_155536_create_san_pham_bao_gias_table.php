<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSanPhamBaoGiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('san_pham_bao_gias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('bao_gia_id')->nullable();
            $table->foreign('bao_gia_id')->references('id')->on('bao_gias')->onDelete('cascade');
            $table->integer('san_pham_id')->nullable();
            $table->foreign('san_pham_id')->references('id')->on('san_phams')->onDelete('cascade');
            $table->double('don_gia')->nullable();
            $table->boolean('lua_chon')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('san_pham_bao_gias');
    }
}
