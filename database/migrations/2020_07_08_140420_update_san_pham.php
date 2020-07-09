<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSanPham extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('san_phams', function (Blueprint $table) {
            $table->double('gia_von')->nullable();
            $table->integer('thuong_hieu_id')->nullable();
            $table->text('vi_tri')->nullable();
        });
        Schema::table('san_pham_don_hang_nha_cung_caps', function ($table) {
            $table->dropColumn('so_luong');
        });
        Schema::table('san_pham_don_hang_nha_cung_caps', function ($table) {
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
        //
    }
}
