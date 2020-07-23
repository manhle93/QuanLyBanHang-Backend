<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXuatHuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xuat_huys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ma')->nullable();
            $table->string('ten')->nullable();
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
        Schema::dropIfExists('xuat_huys');
    }
}
