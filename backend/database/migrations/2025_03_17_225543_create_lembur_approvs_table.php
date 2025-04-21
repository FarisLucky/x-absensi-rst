<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLemburApprovsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lembur_approv', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lembur');
            $table->string('acc_by', 30);
            $table->string('acc_nama', 100);
            $table->dateTime('acc_at');
            $table->string('status');
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
        Schema::dropIfExists('lembur_approv');
    }
}
