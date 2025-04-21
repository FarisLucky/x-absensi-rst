<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIzinBuktiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('izin_bukti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_izin');
            $table->string('nama');
            $table->string('ext', 5);
            $table->string('path', 20);
            $table->string('disk', 10);
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
        Schema::dropIfExists('izin_bukti');
    }
}
