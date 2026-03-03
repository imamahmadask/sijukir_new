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
        Schema::create('trans_insidentil', function (Blueprint $table) {
            $table->id();
            $table->integer('insidentil_id')->unique();
            $table->dateTime('tgl_transaksi');
            $table->string('trx_id')->unique();
            $table->string('issuer', 50);
            $table->integer('jumlah');
            $table->string('status', 25);
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
        Schema::dropIfExists('trans_insidentil');
    }
};
