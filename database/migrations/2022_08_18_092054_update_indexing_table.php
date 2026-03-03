<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIndexingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jukirs', function(Blueprint $table){
            $table->index(['id', 'merchant_id']);
        });

        Schema::table('merchant', function(Blueprint $table){
            $table->index(['id', 'merchant_name']);
        });

        Schema::table('trans_tunai', function(Blueprint $table){
            $table->index('id');
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
