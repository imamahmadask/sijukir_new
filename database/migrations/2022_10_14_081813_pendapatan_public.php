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
        Schema::create('pendapatan_public', function (Blueprint $table) {
            $table->id();
            $table->string('bulan', 20);
            $table->string('tahun', 4);
            $table->bigInteger('pendapatan')->default(0);
            $table->bigInteger('total')->default(0);
            $table->double('persentasi')->default(0);
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
        //
    }
};
