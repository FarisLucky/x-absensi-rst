<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaster2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('m_izin', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 50);
            $table->unsignedTinyInteger('tahunan');
            $table->unsignedTinyInteger('acc_manajemen');
            $table->timestamps();
        });

        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nip', 30);
            $table->string('nama', 100);
            $table->unsignedSmallInteger('id_unit');
            $table->string('unit', 100);
            $table->string('jabatan', 20);
            $table->string('kode_shift', 10)->nullable();
            $table->string('shift', 20)->nullable();
            $table->unsignedSmallInteger('mulai_absen')->nullable();
            $table->string('jam_masuk', 6)->nullable();
            $table->unsignedSmallInteger('telat_masuk')->nullable();
            $table->string('jam_pulang', 6)->nullable();
            $table->unsignedSmallInteger('telat_pulang')->nullable();
            $table->unsignedTinyInteger('libur')->nullable();
            $table->string('created_by', 10)->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->string('status_absen', 6)->nullable();
            $table->string('masuk', 6)->nullable();
            $table->string('pulang', 6)->nullable();
            $table->unsignedTinyInteger('auto')->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->date('tgl_pulang')->nullable();
            $table->unsignedSmallInteger('ttlkerja')->nullable();
            $table->unsignedSmallInteger('ttltelat')->nullable();
            $table->timestamps();
        });

        Schema::create('izin', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30);
            $table->string('nama', 100);
            $table->string('unit', 100);
            $table->string('jabatan', 20);
            $table->smallInteger('idm_izin')->nullable();
            $table->string('izin', 50)->nullable();
            $table->date('mulai');
            $table->date('akhir');
            $table->date('masuk');
            $table->unsignedTinyInteger('periode');
            $table->unsignedTinyInteger('cuti_diambil');
            $table->unsignedTinyInteger('sisa');
            $table->string('ket', 150)->nullable();
            $table->string('acc_nama', 100)->nullable();
            $table->string('acc_nip', 10)->nullable();
            $table->dateTime('acc_at')->nullable();
            $table->string('created_by', 10)->nullable();
            $table->char('acc_status', 1)->nullable();
            $table->timestamps();
        });

        // Schema::create('lembur', function (Blueprint $table) {
        //     $table->id();
        //     $table->date('tanggal')->nullable();
        //     $table->unsignedSmallInteger('id_jenis')->nullable();
        //     $table->string('lembur', 50)->nullable();
        //     $table->string('mulai', 6)->nullable();
        //     $table->date('tgl_akhir',)->nullable();
        //     $table->string('akhir', 6)->nullable();
        //     $table->string('ket', 150)->nullable();
        //     $table->string('nip', 10)->nullable();
        //     $table->string('nama', 100)->nullable();
        //     $table->unsignedSmallInteger('id_unit')->nullable();
        //     $table->string('acc1', 10)->nullable();
        //     $table->string('acc2', 10)->nullable();
        //     $table->dateTime('acc1_at')->nullable();
        //     $table->dateTime('acc2_at')->nullable();
        //     $table->char('acc_status', 1)->nullable();
        //     $table->string('tolak', 200)->nullable();
        //     $table->dateTime('masuk')->nullable();
        //     $table->dateTime('keluar')->nullable();
        //     $table->string('absen_foto', 200)->nullable();
        //     $table->dateTime('absen_foto_at')->nullable();
        //     $table->dateTime('created_by')->nullable();
        //     $table->string('posisi', 50)->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master2');
    }
}
