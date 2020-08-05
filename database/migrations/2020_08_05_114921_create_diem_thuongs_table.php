<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiemThuongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diem_thuongs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ten')->nullable();
            $table->dateTime('bat_dau')->nullable();
            $table->dateTime('ket_thuc')->nullable();
            $table->string('loai1')->nullable();
            $table->string('loai2')->nullable();
            $table->double('muc_hoa_don1')->nullable();
            $table->double('muc_hoa_don2')->nullable();
            $table->double('diem_thuong1')->nullable();
            $table->double('diem_thuong2')->nullable();
            $table->boolean('active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diem_thuongs');
    }
}
