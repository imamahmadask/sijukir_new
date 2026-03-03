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
        Schema::table('jukirs', function(Blueprint $table){
            $table->renameColumn('tgl_pkh_ujipetik', 'tgl_pkh_upl');

            $table->integer('potensi_bulanan')->after('potensi_harian')->default(0);
            $table->integer('potensi_bulanan_upl')->after('uji_petik')->default(0);
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
