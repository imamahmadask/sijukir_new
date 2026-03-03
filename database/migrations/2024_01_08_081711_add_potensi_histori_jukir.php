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
            $table->integer('potensi_harian')->nullable();
            $table->integer('kompensasi')->nullable();
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
            $table->dropColumn('potensi_harian');
            $table->dropColumn('kompensasi');
        });
    }
};
