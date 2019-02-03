<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarangHilangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_hilangs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_user')->nullable();
            $table->unsignedInteger('id_ruangan')->nullable();
            $table->string('name');
            $table->string('keterangan')->nullable();
            $table->dateTime('waktu_penemuan')->nullable();
            $table->dateTime('waktu_diambil')->nullable();
            $table->string('status')->default('new')->nullable();
            $table->string('npm')->nullable();
            $table->string('phone')->nullable();
            $table->string('foto')->nullable();
            $table->string('name_mhs')->nullable();
            $table->timestamps();
            $table->foreign('id_ruangan')
                ->references('id')
                ->on('ruangans')
                ->onDelete('cascade');
            $table->foreign('id_user')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('barang_hilangs');
    }
}
