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
        Schema::create('target_kategoris', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('kategori', 50);
            $table->string('sub_kategori', 50);
            $table->bigInteger('target');
            $table->bigInteger('pencapaian');
            $table->bigInteger('selisih');
            $table->double('persentase');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_kategoris');
    }
};
