<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insidentil', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_pendaftaran');
            $table->string('nik', 25);
            $table->string('nama', 50);
            $table->string('alamat', 50);
            $table->string('tempat_lahir', 25);
            $table->date('tgl_lahir');
            $table->string('jk', 15);
            $table->string('agama', 15);
            $table->string('pekerjaan', 25);
            $table->string('telepon', 15);
            $table->string('nama_perusahaan', 50);
            $table->string('alamat_perusahaan', 50);
            $table->string('akta_perusahaan', 50);
            $table->string('npwp_perusahaan', 25);
            $table->string('nama_acara', 50);
            $table->string('lokasi_acara', 50);
            $table->date('tgl_awal_acara');
            $table->date('tgl_akhir_acara');
            $table->string('waktu_acara', 25);
            $table->string('lokasi_parkir');
            $table->integer('luas_lokasi');
            $table->integer('r2');
            $table->integer('r4');
            $table->integer('potensi');
            $table->string('dokumen')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('status', 25)->default('Unpaid');
            $table->bigInteger('merchant_id');
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
        Schema::dropIfExists('table_insidentil');
    }
};
