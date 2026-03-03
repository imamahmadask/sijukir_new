<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParkirBerlangganansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkir_berlangganans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->string('jenis');
            $table->string('nama_pemilik');
            $table->string('no_pol');
            $table->string('alamat');
            $table->integer('jumlah');
            $table->string('masa_berlaku');
            $table->date('awal_berlaku');
            $table->date('akhir_berlaku');
            $table->date('tgl_dikeluarkan');
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
        Schema::dropIfExists('parkir_berlangganans');
    }
}
