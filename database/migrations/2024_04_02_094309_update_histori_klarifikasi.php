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
        Schema::table('histori_klarifikasi', function (Blueprint $table) {
            $table->renameColumn('surat_peringatan_id', 'surat_klarifikasi_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('histori_klarifikasi', function (Blueprint $table) {
            $table->renameColumn('surat_klarifikasi_id', 'surat_peringatan_id');
        });
    }
};
