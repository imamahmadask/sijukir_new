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
            $table->json('riwayat')->after('tgl_klarifikasi');                        
            $table->integer('kompensasi')->after('riwayat')->nullable();
            $table->integer('banyak_cicilan')->after('status')->nullable();
            $table->json('cicilan')->after('banyak_cicilan')->nullable();
            $table->integer('is_lunas')->after('ket')->default(0);

            $table->renameColumn('status', 'cara_bayar');
            $table->dropColumn('periode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surat_peringatans', function (Blueprint $table) {
            $table->dropColumn('riwayat');
            $table->dropColumn('kompensasi');
            $table->dropColumn('banyak_cicilan');
            $table->dropColumn('cicilan');
            $table->dropColumn('is_lunas');
        });
    }
};
