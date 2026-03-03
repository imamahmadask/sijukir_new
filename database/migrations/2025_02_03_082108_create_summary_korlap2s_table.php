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
        Schema::create('summary_korlaps_2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('korlap_id')->constrained('korlaps')->onDelete('cascade');
            $table->unsignedTinyInteger('bulan');
            $table->year('tahun');
            $table->unsignedInteger('jml_jukir');
            $table->integer('potensi_harian')->default(0);
            $table->integer('potensi_bulanan')->default(0);
            $table->integer('pencapaian')->default(0);
            $table->double('ach')->default(0);
            $table->timestamps();

            $table->unique(['korlap_id', 'bulan', 'tahun']);
            $table->index(['korlap_id', 'tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summary_korlaps_2');
    }
};
