<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDinhMucSanXuatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dinh_muc_san_xuats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('san_pham_id')->nullable();
            $table->foreign('san_pham_id')->references('id')->on('san_phams')->onDelete('cascade');
            $table->integer('nguyen_lieu_id')->nullable();
            $table->foreign('nguyen_lieu_id')->references('id')->on('san_phams')->onDelete('cascade');
            $table->double('so_luong')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dinh_muc_san_xuats');
    }
}
