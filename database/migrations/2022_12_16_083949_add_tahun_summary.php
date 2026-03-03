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
        Schema::table('summary_by_area', function(Blueprint $table){
            $table->string('area', 25)->change();                           
            $table->integer('tahun')->default(0)->after('area');                           
        });

        Schema::table('summary_by_month', function(Blueprint $table){            
            $table->integer('tahun')->change();                           
        });
        
        Schema::table('summary_jukir', function(Blueprint $table){
            $table->integer('tahun')->default(0);                           
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
