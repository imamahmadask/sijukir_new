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
        Schema::table('target', function(Blueprint $table){
            $table->bigInteger('penangguhan_year_before')->after('persentase')->nullable();
            $table->bigInteger('penangguhan_year_after')->after('persentase')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('target', function($table) {
            $table->dropColumn('penangguhan_year_before');
            $table->dropColumn('penangguhan_year_after');
        });
    }
};
