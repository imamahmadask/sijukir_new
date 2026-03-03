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
        Schema::create('surat_peringatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->integer('jukir_id');
            $table->string('tipe', 50);
            $table->string('no_sp', 50);
            $table->date('tanggal_sp');
            $table->string('periode', 50)->nullable();
            $table->integer('kurang_setor')->nullable();
            $table->boolean('is_confirmed')->default(0);
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
        Schema::dropIfExists('surat_peringatan');
    }
};
