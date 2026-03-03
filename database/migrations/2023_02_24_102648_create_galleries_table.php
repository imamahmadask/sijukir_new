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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 7);
            $table->string('judul', 100);
            $table->string('slug', 150);
            $table->text('deskripsi');
            $table->string('kategori', 50);
            $table->date('tanggal');
            $table->string('gambar')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('edited_by', 100)->nullable();
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
        Schema::dropIfExists('galleries');
    }
};
