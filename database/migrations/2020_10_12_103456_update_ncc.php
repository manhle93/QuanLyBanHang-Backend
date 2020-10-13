<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNcc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nha_cung_caps', function (Blueprint $table) {
            $table->date('ngay_chot_cong_no')->nullable();
            $table->date('ngay_thanh_toan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('nha_cung_caps', function (Blueprint $table){
        $table->dropColumn('ngay_chot_cong_no');
        $table->dropColumn('ngay_thanh_toan');
       }); 
    }
}
