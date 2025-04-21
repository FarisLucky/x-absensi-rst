<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTolakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tolak', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_relation');
            $table->string('ket');
            $table->string('jenis', 10);
            $table->string('created_by', 10);
            $table->timestamps();
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->string('validate_by', 10)->nullable();
            $table->dateTime('validate_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tolak');
    }
}
