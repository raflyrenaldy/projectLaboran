<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaporanMasalahLabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_masalah_labs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_ruangan');
            $table->unsignedInteger('id_masalah');
            $table->timestamps();
            $table->foreign('id_masalah')
                ->references('id')
                ->on('masalah_labs')
                ->onDelete('cascade');
            $table->foreign('id_ruangan')
                ->references('id')
                ->on('ruangans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_masalah_labs');
    }
}
