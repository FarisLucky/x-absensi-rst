<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMKaryawanDoksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_karyawandok', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 10);
            $table->unsignedBigInteger('id_karyawan');
            $table->unsignedSmallInteger('id_jenis');
            $table->string('jenis', 50);
            $table->string('file', 150);
            $table->string('size', 10);
            $table->string('ket', 240);
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
        Schema::dropIfExists('m_karyawandok');
    }
}
