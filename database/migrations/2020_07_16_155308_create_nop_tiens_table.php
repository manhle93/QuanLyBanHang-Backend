<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNopTiensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nop_tiens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('id_user_khach_hang')->nullable();
            $table->foreign('id_user_khach_hang')->references('id')->on('users');
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->double('so_tien')->nullable();
            $table->text('noi_dung')->nullable();
            $table->double('so_du')->nullable();
            $table->boolean('trang_thai')->default(true);
            $table->string('ma')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nop_tiens');
    }
}
