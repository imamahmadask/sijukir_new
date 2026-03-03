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
        Schema::table('insidentil', function(Blueprint $table){
            $table->string('alamat_perusahaan', 250)->change();
            $table->string('alamat', 250)->change();
            $table->integer('setoran')->after('potensi');
            $table->string('no_surat')->after('merchant_id')->nullable();
            $table->string('tgl_surat')->after('merchant_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('insidentil', function (Blueprint $table) {
            $table->dropColumn(['setoran', 'no_surat', 'tgl_surat']);
        });
    }
};
