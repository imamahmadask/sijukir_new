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
        Schema::create('summary_korlaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('korlap_id')->constrained('korlaps')->onDelete('cascade');
            $table->unsignedTinyInteger('bulan');
            $table->year('tahun');
            $table->unsignedInteger('jml_jukir');
            $table->unsignedInteger('hijau');
            $table->unsignedInteger('kuning');
            $table->unsignedInteger('merah');
            $table->float('ach_hijau');
            $table->float('ach_kuning');
            $table->float('ach_merah');
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
        Schema::dropIfExists('summary_korlaps');
    }
};
