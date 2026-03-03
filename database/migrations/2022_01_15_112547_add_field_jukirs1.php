<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldJukirs1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jukirs', function(Blueprint $table){
            $table->string('merchant_id');
            $table->string('jenis_kelamin')->nullable();
            $table->string('no_perjanjian')->nullable();
            $table->date('tgl_perjanjian')->nullable();
            $table->date('tgl_akhir_perjanjian')->nullable();
            $table->string('potensi_harian');
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
