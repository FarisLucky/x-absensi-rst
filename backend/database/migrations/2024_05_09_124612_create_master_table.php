<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_unit', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 100)->index('nama');
            $table->unsignedBigInteger('id_parent')->nullable();
            $table->timestamps();
        });

        Schema::create('m_karyawan', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('id_unit');
            $table->string('unit', 100);
            $table->string('jabatan', 20);
            $table->string('nip', 30)->unique();
            $table->string('nik', 20)->unique();
            $table->string('nama', 100);
            $table->char('sex', 1);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tgl_lahir');
            $table->string('pendidikan', 165)->nullable(true);
            $table->string('alamat', 165)->nullable(true);
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('desa', 50)->nullable();
            $table->string('kec', 50)->nullable();
            $table->string('kab', 50)->nullable();
            $table->string('kodepos', 6)->nullable();
            $table->string('prov', 50)->nullable();
            $table->string('telp', 20)->nullable();
            $table->string('tgl_masuk', 20)->nullable();
            $table->string('tgl_resign', 20)->nullable();
            $table->string('email', 70)->nullable();
            $table->char('jml_anak', 1)->nullable(true);
            $table->unsignedTinyInteger('jml_cuti')->nullable(true);
            $table->string('photo', 253)->nullable(true);
            $table->string('status_kerja', 20)->nullable(true);
            $table->timestamps();
        });

        Schema::create('m_karyawan_fams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_karyawan');
            $table->string('ibu', 100)->nullable();
            $table->string('pasangan', 10)->nullable();
            $table->string('nama_pasangan', 10)->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->string('tgl_lahir', 10)->nullable();
            $table->string('ank1', 10)->nullable();
            $table->string('nik_ank1', 20)->nullable();
            $table->string('tempat_lahir_ank1', 100)->nullable();
            $table->string('tgl_lahir_ank1', 10)->nullable();
            $table->string('ank2', 10)->nullable();
            $table->string('nik_ank2', 20)->nullable();
            $table->string('tempat_lahir_ank2', 100)->nullable();
            $table->string('tgl_lahir_ank2', 10)->nullable();
            $table->string('ank3', 10)->nullable();
            $table->string('nik_ank3', 20)->nullable();
            $table->string('tempat_lahir_ank3', 100)->nullable();
            $table->string('tgl_lahir_ank3', 10)->nullable();
            $table->string('ank4', 10)->nullable();
            $table->string('nik_ank4', 20)->nullable();
            $table->string('tempat_lahir_ank4', 100)->nullable();
            $table->string('tgl_lahir_ank4', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('m_shift', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('kode', 10);
            $table->string('nama', 20);
            $table->smallInteger('mulai_absen');
            $table->string('jam_masuk', 6);
            $table->smallInteger('telat_masuk');
            $table->string('jam_pulang', 6);
            $table->smallInteger('telat_pulang');
            $table->timestamps();
        });

        Schema::create('wilayah', function (Blueprint $table) {
            $table->string('kode', 13);
            $table->string('nama', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master');
    }
}
