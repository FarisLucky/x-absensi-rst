<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jadwal');
            $table->string('nama', 20);
            $table->string('latlng_masuk', 30);
            $table->string('latlng_pulang', 30);
            $table->string('lok_masuk', 50)->nullable();
            $table->string('lok_pulang', 50)->nullable();
            $table->string('manufact', 50);
            $table->string('model', 50);
            $table->string('platform', 10);
            $table->string('osVersion', 10);
            $table->string('ip', 15);
            $table->timestamps();
        });

        Schema::create('presensi_terlambat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jadwal');
            $table->string('jenis', 7)->comment('masuk atau pulang');
            $table->string('ket');
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
        Schema::dropIfExists('presensi');
    }
}
