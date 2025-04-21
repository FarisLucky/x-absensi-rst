<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResetDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reset_device', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 10);
            $table->string('nama', 100);
            $table->string('platform', 25)->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('ket', 50)->nullable();
            $table->smallInteger('status')->nullable();
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
        Schema::dropIfExists('reset_device');
    }
}
