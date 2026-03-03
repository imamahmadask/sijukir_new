<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLokasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lokasis', function (Blueprint $table) {
            $table->id();
            $table->string('titik_parkir');
            $table->string('lokasi_parkir');
            $table->string('jenis_lokasi');
            $table->string('waktu_pelayanan');
            $table->string('dasar_ketetapan');
            $table->string('no_ketetapan');
            $table->string('kode_maps');
            $table->string('status');
            $table->string('gambar');
            $table->date('tgl_registrasi');
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
        Schema::dropIfExists('lokasis');
    }
}
