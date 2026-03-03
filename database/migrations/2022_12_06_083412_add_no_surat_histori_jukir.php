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
            $table->string('no_surat', 50)->after('tgl_histori');
                             
            $table->string('jenis_histori', 50)->change();                 
            $table->string('created_by', 50)->change();                 
            $table->string('edited_by', 50)->change();                 
            $table->integer('jukir_id')->change();                 
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
