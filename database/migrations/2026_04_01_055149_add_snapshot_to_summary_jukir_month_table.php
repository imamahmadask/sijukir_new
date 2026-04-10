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
        Schema::table('summary_jukir_month', function (Blueprint $table) {
            $table->string('ket_jukir')->nullable()->default('Active')->after('kurang_setor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('summary_jukir_month', function (Blueprint $table) {
            $table->dropColumn('ket_jukir');
        });
    }
};
