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
        Schema::table('insidentil', function(Blueprint $table){
            $table->string('jenis_izin', 50)->after('telepon');
            $table->string('kriteria_lokasi', 50)->after('lokasi_parkir');
            $table->integer('jumlah_hari')->after('lokasi_acara');
            $table->string('status', 25)->default('Pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('insidentil', function (Blueprint $table) {
            $table->dropColumn(['jenis_izin', 'kriteria_lokasi', 'jumlah_hari']);
        });
    }
};
