<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFieldLokasi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lokasis', function(Blueprint $table){
            $table->string('hari_kerja')->nullable();
            $table->renameColumn('doc_1', 'document');
            $table->dropColumn('foto_1');
            $table->dropColumn('foto_2');
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
