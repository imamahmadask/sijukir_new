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
        Schema::table('pajak_parkirs', function (Blueprint $table) {
            $table->string('nama_pengelola')->nullable()->after('nama_objek');
            $table->json('tarif')->nullable()->after('nama_pengelola');
            $table->string('no_rekomendasi')->nullable()->after('keterangan');
            $table->date('tgl_rekomendasi')->nullable()->after('no_rekomendasi');
            $table->string('file_document')->nullable()->after('tgl_rekomendasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pajak_parkirs', function (Blueprint $table) {
            $table->dropColumn(['nama_pengelola', 'tarif', 'no_rekomendasi', 'tgl_rekomendasi', 'file_document']);
        });
    }
};
