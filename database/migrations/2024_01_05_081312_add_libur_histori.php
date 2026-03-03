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
        Schema::table('histori_jukir', function(Blueprint $table){
            $table->integer('jml_hari_libur')->nullable();
            $table->integer('tahun_libur')->nullable();
            $table->date('tgl_awal_libur')->nullable();
            $table->date('tgl_akhir_libur')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('histori_jukir', function($table) {
            $table->dropColumn('jml_hari_libur');
            $table->dropColumn('tahun_libur');
            $table->dropColumn('hari_awal_libur');
            $table->dropColumn('hari_akhir_libur');
        });
    }
};
