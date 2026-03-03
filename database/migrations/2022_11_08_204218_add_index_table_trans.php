<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexTableTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trans_tunai', function (Blueprint $table) {
            $table->index('area_id');
        });

        Schema::table('trans_non_tunai', function (Blueprint $table) {
            $table->index('area_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trans_tunai', function (Blueprint $table) {
            $table->dropIndex('area_id');
        });

        Schema::table('trans_non_tunai', function (Blueprint $table) {
            $table->dropIndex('area_id');
        });
    }
}
