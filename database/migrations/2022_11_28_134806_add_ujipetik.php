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
        Schema::table('jukirs', function(Blueprint $table){
            $table->integer('uji_petik')->after('potensi_harian')->default(0);             
            $table->date('tgl_pkh_ujipetik')->after('uji_petik')->nullable();
            
            $table->string('kode_jukir', 15)->change();
            $table->string('nik_jukir', 30)->change();
            $table->string('nama_jukir', 50)->change();
            $table->string('tempat_lahir', 25)->change();
            $table->date('tgl_lahir')->change();
            $table->string('alamat', 100)->change();
            $table->string('kel_alamat', 25)->change();
            $table->string('kec_alamat', 25)->change();
            $table->string('kab_kota_alamat', 25)->change();
            $table->string('telepon', 15)->change();
            $table->string('agama', 15)->change();
            $table->string('jenis_jukir', 20)->change();
            $table->string('status', 20)->change();
            $table->string('jenis_kelamin', 20)->change();
            $table->string('no_perjanjian', 50)->change();
            $table->string('ket_jukir', 25)->change();
            $table->string('waktu_kerja', 15)->change();
            $table->string('hari_libur', 50)->change();
            $table->integer('jml_hari_kerja')->change();
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
