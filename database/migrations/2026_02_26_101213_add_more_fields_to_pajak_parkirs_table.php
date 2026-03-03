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
            $table->string('npwpd')->nullable()->after('alamat_objek');
            $table->text('link_google_maps')->nullable()->after('longitude');
            $table->string('jenis_tarif')->nullable()->after('tarif');
            $table->string('foto_lokasi')->nullable()->after('jenis_tarif');
            $table->json('srp')->nullable()->after('foto_lokasi');
            $table->string('file_rekomendasi')->nullable()->after('file_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pajak_parkirs', function (Blueprint $table) {
            $table->dropColumn(['npwpd', 'link_google_maps', 'jenis_tarif', 'foto_lokasi', 'srp', 'file_rekomendasi']);
        });
    }
};
