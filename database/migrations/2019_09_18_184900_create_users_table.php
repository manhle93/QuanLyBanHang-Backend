<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('avatar_url')->nullable();
            $table->boolean('active')->default(false);
            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->unsignedInteger('tinh_thanh_id')->nullable();
            $table->foreign('tinh_thanh_id')->references('id')->on('tinh_thanhs');
            $table->unsignedInteger('quan_huyen_id')->nullable();
            $table->foreign('quan_huyen_id')->references('id')->on('quan_huyens');
            $table->unsignedInteger('toa_nha_id')->nullable();
            $table->foreign('toa_nha_id')->references('id')->on('toa_nhas');
            // $table->string('email_token');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
