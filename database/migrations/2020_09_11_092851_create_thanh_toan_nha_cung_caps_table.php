<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThanhToanNhaCungCapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thanh_toan_nha_cung_caps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('ma_don')->nullable();
            $table->double('phai_thanh_toan')->nullable();
            $table->double('thanh_toan')->nullable();
            $table->integer('nha_cung_cap_id')->nullable();
            $table->foreign('nha_cung_cap_id')->references('id')->on('nha_cung_caps')->onDelete('cascade');
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thanh_toan_nha_cung_caps');
    }
}
