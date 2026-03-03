<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableKorlaps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('korlaps', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik');
            $table->string('alamat');
            $table->string('telepon');
            $table->string('foto');
            $table->string('created_by');
            $table->string('edited_by');
            $table->timestamps();
        });

        Schema::table('lokasis', function (Blueprint $table) {
            $table->string('korlap_id')->after('kelurahan_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_korlaps');
    }
}
