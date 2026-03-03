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
        Schema::table('parkir_berlangganans', function(Blueprint $table){
            $table->string('keterangan', 100)->after('tgl_dikeluarkan')->nullable();
            $table->renameColumn('nama_pemilik', 'nama');                 

            $table->string('nomor', 100)->change();                 
            $table->string('jenis', 25)->change();                 
            $table->string('no_pol', 11)->change();                 
            $table->string('alamat', 50)->change();                 
            $table->string('status', 100)->change();                 
            $table->bigInteger('jumlah')->change();                 
            $table->string('masa_berlaku', 10)->change();                 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parkir_berlangganans', function(Blueprint $table){
            $table->dropColumn('keterangan');
            $table->renameColumn('nama', 'nama_pemilik');                 

            $table->string('nomor', 100)->change();                 
            $table->string('jenis', 25)->change();                 
            $table->string('no_pol', 11)->change();                 
            $table->string('alamat', 50)->change();                 
            $table->string('status', 100)->change();                 
            $table->bigInteger('jumlah')->change();                 
            $table->string('masa_berlaku', 10)->change();                 
        });
    }
};
