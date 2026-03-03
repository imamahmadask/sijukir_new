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
            $table->bigInteger('tunai')->change();
            $table->bigInteger('non_tunai')->change();
            $table->bigInteger('total')->change();
        });

        Schema::table('summary_by_month', function(Blueprint $table){
            $table->bigInteger('tunai')->change();
            $table->bigInteger('non_tunai')->change();
            $table->bigInteger('total')->change();
        });
        
        Schema::table('summary_day', function(Blueprint $table){            
            $table->bigInteger('total')->change();
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
