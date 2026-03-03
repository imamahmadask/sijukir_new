<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTahunSummaryMonth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_by_month', function (Blueprint $table) {
            $table->string('tahun', 4)->after('bulan')->nullable();
        });

        Schema::table('summary_by_area', function (Blueprint $table) {
            $table->integer('area_id')->after('total')->nullable();
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
