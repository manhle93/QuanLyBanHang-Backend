<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLichSuHoatDongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lich_su_hoat_dongs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('reference_id')->nullable();
            $table->string('hanh_dong')->nullable();
            $table->text('noi_dung')->nullable();
            $table->string('type')->nullable();
            $table->unsignedInteger('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lich_su_hoat_dongs');
    }
}
