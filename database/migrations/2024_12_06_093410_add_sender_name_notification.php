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
        Schema::table('receive_notif_api', function (Blueprint $table) {
            $table->string('sender_name')->after('tgl_notif')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receive_notif_api', function (Blueprint $table) {
            $table->dropColumn('sender_name');
        });
    }
};
