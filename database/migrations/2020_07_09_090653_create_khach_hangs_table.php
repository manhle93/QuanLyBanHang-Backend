<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKhachHangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('khach_hangs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ma')->nullable();
            $table->string('ten')->nullable();
            $table->text('dia_chi')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->string('anh_dai_dien')->nullable();
            $table->string('ma_so_thue')->nullable();
            $table->string('email')->nullable();
            $table->string('facebook')->nullable();
            $table->integer('nhom_id')->nullable();
            $table->boolean('gioi_tinh')->nullable();
            $table->boolean('ca_nhan')->default(true);
            $table->text('ghi_chu')->nullable();
            $table->date('ngay_sinh')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->dateTime('giao_dich_cuoi')->nullable();
            $table->string('so_tai_khoan')->nullable();
            $table->double('so_du')->nullable();
            $table->dateTime('chuyen_khoan_cuoi')->nullable();
            $table->integer('loai_thanh_vien_id')->nullable();
            $table->string('tin_nhiem')->nullable();
            $table->double('diem_quy_doi')->nullable();
            $table->double('tien_vay')->nullable();
            $table->string('trang_thai')->nullable();
            $table->integer('nguoi_tao_id')->nullable();
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('khach_hangs');
    }
}
