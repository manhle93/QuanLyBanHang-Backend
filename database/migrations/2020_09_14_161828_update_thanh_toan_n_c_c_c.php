<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateThanhToanNCCC extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('don_hang_thanh_toan_nha_cung_caps', function (Blueprint $table) {
            $table->string('loai')->nullable();
        });

        Schema::table('tra_hang_nha_cung_caps', function (Blueprint $table) {
            $table->boolean('thanh_toan')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
