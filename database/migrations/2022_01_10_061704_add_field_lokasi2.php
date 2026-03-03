<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldLokasi2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lokasis', function(Blueprint $table){
            $table->string('ket_lokasi');
            $table->string('doc_1')->nullable();
            $table->string('doc_2')->nullable();
            $table->string('doc_3')->nullable();
            $table->string('doc_4')->nullable();
            $table->string('foto_1')->nullable();
            $table->string('foto_2')->nullable();
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
