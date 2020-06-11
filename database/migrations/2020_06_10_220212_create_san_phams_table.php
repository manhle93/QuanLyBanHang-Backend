<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSanPhamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('san_phams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('ten_san_pham')->nullable();
            $table->integer('danh_muc_id')->nullable();
            $table->foreign('danh_muc_id')->references('id')->on('danh_muc_san_phams')->onDelete('cascade');
            $table->text('anh_dai_dien')->nullable();
            $table->double('gia_ban')->nullable();
            $table->double('gia_sale')->nullable();
            $table->string('don_vi_tinh')->nullable();
            $table->boolean('dang_khuyen_mai')->default(false);
            $table->text('mo_ta_san_pham')->nullable();
            $table->dateTime('bat_dau_khuyen_mai')->nullable();
            $table->dateTime('ket_thuc_khuyen_mai')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('san_phams');
    }
}
