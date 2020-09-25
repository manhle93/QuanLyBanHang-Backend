<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThanhToanBoXungsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thanh_toan_bo_xungs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('don_hang_id')->nullable();
            $table->foreign('don_hang_id')->references('id')->on('don_dat_hangs')->onDelete('cascade');
            $table->text('noi_dung')->nullable();
            $table->double('so_tien')->nullable();
            $table->string('hinh_thuc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thanh_toan_bo_xungs');
    }
}
