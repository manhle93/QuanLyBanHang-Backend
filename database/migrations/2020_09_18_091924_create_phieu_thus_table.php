<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhieuThusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phieu_thus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('type')->nullable();
            $table->integer('reference_id')->nullable();
            $table->double('so_tien')->nullable();
            $table->text('noi_dung')->nullable();
            $table->text('thong_tin_giao_dich')->nullable();
            $table->integer('user_id_khach_hang')->nullable();
            $table->text('thong_tin_khach_hang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phieu_thus');
    }
}
