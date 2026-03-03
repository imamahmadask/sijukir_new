<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_area_month', function (Blueprint $table) {
            $table->id();
            $table->integer('area_id');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('potensi')->default(0);
            $table->bigInteger('tunai')->default(0);
            $table->bigInteger('non_tunai')->default(0);
            $table->bigInteger('total')->default(0);
            $table->bigInteger('kurang_setor')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summary_area_month');
    }
};
