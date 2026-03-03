<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTypeColumnTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trans_non_tunai', function(Blueprint $table){
            $table->bigInteger('total_nilai')->change();
        });

        Schema::table('trans_tunai', function(Blueprint $table){
            $table->integer('jukir_id')->change();
        });

        Schema::table('jukirs', function(Blueprint $table){
            $table->integer('lokasi_id')->change();
            $table->integer('potensi_harian')->change();
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
}
