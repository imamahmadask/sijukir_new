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
        Schema::table('surat_peringatans', function(Blueprint $table){
            $table->dropColumn('merchant_name');            
            $table->dropColumn('nama');            
            $table->dropColumn('telepon');            
            $table->dropColumn('titik');            
            $table->dropColumn('lokasi');
            $table->dropColumn('hasil_klarifikasi');
            
            $table->string('no_surat', 100)->change();
            $table->string('tipe', 50)->change();            
            $table->string('periode', 100)->change();            
            $table->string('jml_kurang_setor', 20)->change();            
            $table->string('total_bayar', 20)->change();            
            $table->string('status', 50)->change();       
            $table->string('created_by', 50)->change();       
            $table->string('edited_by', 50)->change();       
            $table->text('ket')->change();       
            
            $table->integer('jukir_id');
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
