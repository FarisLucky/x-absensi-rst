<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIzinDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('izin_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_izin')->nullable();
            $table->date('tanggal');
            $table->string('nip', 10)->nullable();
            $table->string('pengganti', 10)->nullable();
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
        Schema::dropIfExists('izin_detail');
    }
}
