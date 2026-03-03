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
        Schema::create('receive_notif_api', function (Blueprint $table) {
            $table->id();
            $table->string('syslog', 50);
            $table->dateTime('tgl_transaksi');
            $table->bigInteger('merchant_id')->unsigned();
            $table->string('merchant_name', 50);
            $table->integer('jumlah');
            $table->string('issuer_name', 50);
            $table->string('status', 25);
            $table->text('pesan_notif');
            $table->dateTime('tgl_notif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receive_notif_api');
    }
};
