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
        Schema::table('summary_jukir_month', function (Blueprint $table) {
            $table->string('status_jukir')->default('Active')->after('jukir_id');
            $table->string('tipe_jukir')->default('Non-Tunai')->after('status_jukir');
            $table->integer('korlap_id')->nullable()->after('tipe_jukir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('summary_jukir_month', function (Blueprint $table) {
            $table->dropColumn(['status_jukir', 'tipe_jukir', 'korlap_id']);
        });
    }
};
