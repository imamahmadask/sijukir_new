<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummaryDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_day', function (Blueprint $table) {
            $table->id()->index();
            $table->date('tanggal');
            $table->integer('jml_transaksi')->default(0);
            $table->integer('jml_jukir')->default(0);
            $table->integer('total')->default(0);
            $table->integer('average_trx')->default(0);
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
        Schema::dropIfExists('summary_day');
    }
}
