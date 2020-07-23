<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSanPhamXuatHuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('san_pham_xuat_huys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('xuat_huy_id')->nullable();
            $table->foreign('xuat_huy_id')->references('id')->on('xuat_huys')->onDelete('cascade');
            $table->integer('san_pham_id')->nullable();
            $table->foreign('san_pham_id')->references('id')->on('san_phams')->onDelete('cascade');
            $table->double('so_xuat_huy')->nullable();
            $table->double('ton_kho_truoc_xuat_huy')->nullable();
            $table->double('ton_kho_sau_xuat_huy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('san_pham_xuat_huys');
    }
}
