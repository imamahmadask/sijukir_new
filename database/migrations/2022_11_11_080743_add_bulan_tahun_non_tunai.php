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
        Schema::table('trans_non_tunai', function(Blueprint $table){
            $table->string('merchant_name', 25)->change();
            $table->string('status', 25)->change();
            $table->string('kecamatan', 25)->change();
            $table->string('filename', 50)->change();
            $table->integer('bulan')->after('tgl_transaksi')->nullable();
            $table->integer('tahun')->after('bulan')->nullable();
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trans_non_tunai', function (Blueprint $table) {
            $table->dropIndex('bulan');
            $table->dropIndex('tahun');
        });
    }
};
