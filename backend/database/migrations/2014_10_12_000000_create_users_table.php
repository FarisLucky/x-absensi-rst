<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->smallIncrements('id');
            $table->string('nama', 50);
            $table->string('nip', 10)->unique();
            $table->string('password');
            $table->string('device_hash')->nullable();
            $table->string('platform')->nullable();
            $table->string('model')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->string('role', 30)->nullable();
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
