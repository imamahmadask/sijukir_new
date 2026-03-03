<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_by_area', function(Blueprint $table){
            $table->integer('tunai')->default(0)->change();
            $table->integer('non_tunai')->default(0)->change();
            $table->integer('total')->default(0)->change();
        });

        Schema::table('summary_by_month', function(Blueprint $table){
            $table->integer('tunai')->default(0)->change();
            $table->integer('non_tunai')->default(0)->change();
            $table->integer('total')->default(0)->change();
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
