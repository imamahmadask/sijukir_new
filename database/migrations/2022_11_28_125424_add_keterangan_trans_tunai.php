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
            $table->string('keterangan', 100)->after('selisih')->default('-');
            
            $table->integer('selisih')->default(0)->change();    
            $table->string('no_kwitansi', 50)->change();    
            $table->date('tgl_transaksi')->change();    
            $table->bigInteger('jumlah_transaksi')->change();    

            $table->dropColumn('kecamatan');       
            $table->dropColumn('file_kwitansi');       
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
};
