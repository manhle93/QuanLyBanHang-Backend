<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonDatHangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('don_dat_hangs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ma')->nullable();
            $table->double('tong_tien')->nullable();
            $table->string('ten')->nullable();
            $table->string('nguoi_mua_hang')->nullable();
            $table->integer('user_id')->nullable();
            $table->text('dia_chi')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->double('diem_quy_doi')->nullable();
            $table->double('giam_gia')->nullable();
            $table->double('da_thanh_toan')->nullable();
            $table->string('trang_thai')->nullable();
            $table->double('con_phai_thanh_toan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('don_dat_hangs');
    }
}
