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
        Schema::table('potensi_titiks', function (Blueprint $table) {
            $table->integer('is_register')->after('info_by')->nullable();
            $table->string('status', 100)->after('info_by')->nullable();
            $table->date('tgl_terdaftar')->after('is_register')->nullable();
            $table->date('tgl_ujicoba')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('potensi_titiks', function (Blueprint $table) {
            $table->dropColumn('is_register');
            $table->dropColumn('status');
            $table->dropColumn('tgl_terdaftar');
            $table->dropColumn('tgl_ujicoba');
        });
    }
};
