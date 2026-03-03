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
        Schema::create('potensi_titiks', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_terdata');
            $table->string('titik', 50);
            $table->string('lokasi', 50);
            $table->string('slug', 100);
            $table->string('jenis_lokasi', 50);
            $table->string('kategori', 50);
            $table->string('kord_lat', 100)->nullable();
            $table->string('kord_long', 100)->nullable();
            $table->string('google_maps')->nullable();
            $table->string('gambar')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('info_by', 50)->nullable();
            $table->integer('area_id');
            $table->integer('kelurahan_id');
            $table->integer('korlap_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potensi_titiks');
    }
};
