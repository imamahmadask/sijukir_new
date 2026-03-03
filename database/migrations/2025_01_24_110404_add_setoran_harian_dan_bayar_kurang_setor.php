<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('summary_jukir', function (Blueprint $table) {
            $table->integer('setoran_harian')->after('non_tunai')->nullable();
            $table->integer('bayar_kurang_setor')->after('setoran_harian')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('summary_jukir', function (Blueprint $table) {
            $table->dropColumn('setoran_harian');
            $table->dropColumn('bayar_kurang_setor');
        });
    }
};
