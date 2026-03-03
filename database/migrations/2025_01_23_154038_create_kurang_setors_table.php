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
        Schema::create('kurang_setors', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('jukir_id');
            $table->bigInteger('tahun');
            $table->date('tgl_setor');
            $table->bigInteger('jumlah');
            $table->bigInteger('histori_jukir_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurang_setors');
    }
};
