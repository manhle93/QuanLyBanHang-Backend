<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNhaCungCapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nha_cung_caps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ten')->nullable();
            $table->string('ma')->nullable();
            $table->text('dia_chi')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->string('anh_dai_dien')->nullable();
            $table->string('ma_so_thue')->nullable();
            $table->string('email')->nullable();
            $table->string('cong_ty')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->integer('nguoi_tao_id')->nullable();
            $table->boolean('active')->default(true);
            $table->string('tin_nhiem')->nullable();
            $table->string('trang_thai')->nullable();
            $table->unsignedInteger('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nha_cung_caps');
    }
}
