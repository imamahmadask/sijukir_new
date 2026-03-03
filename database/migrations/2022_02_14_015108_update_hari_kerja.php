<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHariKerja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jukirs', function(Blueprint $table){
            $table->integer('jml_hari_kerja')->nullable();
            $table->string('hari_kerja')->nullable();
        });

        Schema::table('lokasis', function(Blueprint $table){
            $table->renameColumn('hari_kerja', 'hari_buka');

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
