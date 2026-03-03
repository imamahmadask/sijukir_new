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
            $table->integer('potensi')->after('jukir_id')->nullable();
            $table->integer('kurang_setor')->after('total')->nullable();
            $table->integer('kompensasi')->after('non_tunai')->nullable();
            $table->double('persentase')->after('kurang_setor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('summary_jukir', function (Blueprint $table) {
            $table->dropColumn('potensi');
            $table->dropColumn('kurang_setor');
            $table->dropColumn('kompensasi');
            $table->dropColumn('persentase');
        });
    }
};
