<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLemburReqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lembur_req', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30);
            $table->string('nama', 100);
            $table->string('unit', 100);
            $table->date('tanggal');
            $table->dateTime('mulai');
            $table->dateTime('akhir');
            $table->tinyInteger('ttl_jam');
            $table->tinyInteger('status');
            $table->smallInteger('ttl_menit');
            $table->string('catatan', 200);
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
        Schema::dropIfExists('lembur_req');
    }
}
