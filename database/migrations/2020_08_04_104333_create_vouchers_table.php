<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ma')->nullable();
            $table->integer('so_luong')->nullable();
            $table->string('ap_dung_cho')->nullable();
            $table->double('don_toi_thieu')->nullable();
            $table->dateTime('bat_dau')->nullable();
            $table->dateTime('ket_thuc')->nullable();
            $table->text('mo_ta')->nullable();
            $table->string('loai')->nullable();
            $table->double('giam_gia')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
