<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJukirTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jukirs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jukir');
            $table->string('nik_jukir');
            $table->string('nama_jukir');
            $table->string('tempat_lahir');
            $table->string('tgl_lahir');
            $table->string('alamat');
            $table->string('kel_alamat');
            $table->string('kec_alamat');
            $table->string('telepon');
            $table->string('agama');
            $table->string('jenis_jukir');
            $table->string('status');
            $table->string('foto');
            $table->string('lokasi_id');
            $table->string('doc_1');
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
        Schema::dropIfExists('jukir');
    }
}
