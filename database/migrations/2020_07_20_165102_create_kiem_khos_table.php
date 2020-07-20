<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKiemKhosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kiem_khos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ma')->nullable();
            $table->string('ten')->nullable();
            $table->string('trang_thai')->nullable();
            $table->integer('user_nhan_vien_id')->nullable();
            $table->foreign('user_nhan_vien_id')->references('id')->on('users');
            $table->integer('user_tao_id')->nullable();
            $table->foreign('user_tao_id')->references('id')->on('users');
            $table->text('ghi_chu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kiem_khos');
    }
}
