<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSummaryJukir extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_jukir', function(Blueprint $table){
            $table->integer('jukir_id')->change();
            $table->integer('tunai')->default(0)->change();
            $table->bigInteger('non_tunai')->default(0)->change();
            $table->integer('jml_transaksi')->default(0)->change();
            $table->bigInteger('total')->default(0)->change();
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
