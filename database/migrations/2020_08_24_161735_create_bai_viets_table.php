<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaiVietsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bai_viets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('tieu_de')->nullable();
            $table->text('noi_dung')->nullable();
            $table->boolean('xuat_ban')->default(true);
            $table->text('anh_dai_dien')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bai_viets');
    }
}
