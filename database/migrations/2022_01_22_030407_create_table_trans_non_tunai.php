<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTransNonTunai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trans_non_tunai', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_transaksi');
            $table->string('merchant_id');
            $table->string('merchant_name');
            $table->string('issuer_name');
            $table->string('total_nilai');
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
        Schema::dropIfExists('table_trans_non_tunai');
    }
}
