<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSanPhamDonHangNhaCungCapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('san_pham_don_hang_nha_cung_caps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('san_pham_id')->nullable();
            $table->string('so_luong')->nullable();
            $table->double('don_gia')->nullable();
            $table->json('hinh_anh')->nullable();
            $table->integer('don_hang_id')->nullable();
            $table->foreign('don_hang_id')->references('id')->on('don_hang_nha_cung_caps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('san_pham_don_hang_nha_cung_caps');
    }
}
