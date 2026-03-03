<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabelTransTunai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trans_tunai', function (Blueprint $table) {
            $table->id();
            $table->string('tgl_transaksi');
            $table->integer('jumlah_transaksi');
            $table->string('no_kwitansi');
            $table->string('file_kwitansi');
            $table->string('jukir_id');
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
        Schema::dropIfExists('tabel_trans_tunai');
    }
}
