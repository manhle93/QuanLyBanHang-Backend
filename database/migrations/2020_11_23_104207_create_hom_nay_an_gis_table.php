<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomNayAnGisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hom_nay_an_gis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('ten')->nullable();
            $table->integer('so_nguoi_an')->nullable();
            $table->json('nguyen_lieu')->nullable();
            $table->text('mo_ta')->nullable();
            $table->string('anh_dai_dien')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hom_nay_an_gis');
    }
}
