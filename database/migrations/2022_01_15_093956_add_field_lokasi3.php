<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldLokasi3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lokasis', function(Blueprint $table){
            $table->string('sisi')->nullable();
            $table->string('panjang_luas')->nullable();
            $table->text('google_maps')->nullable();
            $table->date('tgl_ketetapan')->nullable();
            $table->dropColumn('doc_2')->nullable();
            $table->dropColumn('doc_3')->nullable();
            $table->dropColumn('doc_4')->nullable();

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
