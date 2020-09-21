<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoiTraHangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doi_tra_hangs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('user_id_khach_hang')->nullable();
            $table->foreign('user_id_khach_hang')->references('id')->on('users')->onDelete('cascade');
            $table->integer('don_hang_id')->nullable();
            $table->foreign('don_hang_id')->references('id')->on('don_dat_hangs')->onDelete('cascade');
            $table->integer('san_pham_id')->nullable();
            $table->foreign('san_pham_id')->references('id')->on('san_phams')->onDelete('cascade');
            $table->double('gia_ban')->nullable();
            $table->double('so_luong')->nullable();
            $table->double('doanh_thu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doi_tra_hangs');
    }
}
