<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProsesPengaduan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengaduan', function(Blueprint $table){
            $table->string('tgl_diproses')->after('status')->nullable();
            $table->string('tgl_selesai_proses')->after('tgl_diproses')->nullable();
            $table->string('edited_by')->after('tgl_selesai_proses')->nullable();
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
