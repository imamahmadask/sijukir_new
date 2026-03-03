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
        Schema::table('trans_tunai', function(Blueprint $table){
            $table->text('keterangan')->change();
        });

        Schema::table('lokasis', function(Blueprint $table){
            $table->string('titik_parkir', 100)->change();
            $table->string('lokasi_parkir', 100)->change();
            $table->string('slug', 100)->change();
            $table->string('jenis_lokasi', 50)->change();
            $table->string('waktu_pelayanan', 50)->change();
            $table->string('dasar_ketetapan', 50)->change();
            $table->string('no_ketetapan', 50)->change();
            $table->string('kord_lat', 50)->change();
            $table->string('kord_long', 50)->change();
            $table->string('status', 50)->change();
            $table->string('gambar', 100)->change();
            $table->integer('area_id')->change();
            $table->integer('korlap_id')->change();
            $table->string('ket_lokasi', 50)->change();
            $table->string('sisi', 50)->change();
            $table->string('panjang_luas', 25)->change();
            $table->string('hari_buka', 25)->change();

            $table->dropColumn('kode_maps');
            $table->dropColumn('document');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
