<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratPeringatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_peringatans', function (Blueprint $table) {
            $table->id();
            $table->string('tipe');
            $table->string('periode');
            $table->string('no_surat');
            $table->string('merchant_name');
            $table->string('nama');
            $table->string('telepon');
            $table->string('titik');
            $table->string('lokasi');
            $table->date('tgl_klarifikasi');
            $table->string('jml_kurang_setor');
            $table->string('hasil_klarifikasi');
            $table->string('total_bayar')->nullable();
            $table->date('batas_setor');
            $table->string('status')->default('belum lunas');
            $table->string('ket')->nullable();
            $table->string('created_by')->nullable();
            $table->string('edited_by')->nullable();
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
        Schema::dropIfExists('surat_peringatans');
    }
}
