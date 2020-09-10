<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraHangNhaCungCapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tra_hang_nha_cung_caps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('ma_don')->nullable();
            $table->integer('nha_cung_cap_id')->nullable();
            $table->foreign('nha_cung_cap_id')->references('id')->on('nha_cung_caps')->onDelete('cascade');
            $table->double('tong_tien')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tra_hang_nha_cung_caps');
    }
}
