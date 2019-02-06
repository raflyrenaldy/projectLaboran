<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUangKasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uang_kas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_koperasi')->nullable();
            $table->unsignedInteger('id_pembelian')->nullable();
            $table->string('name');
            $table->bigInteger('kas_masuk')->default(0)->nullable();
            $table->bigInteger('kas_keluar')->default(0)->nullable();
            $table->bigInteger('saldo');
            $table->timestamps();
            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('id_koperasi')
                ->references('id')
                ->on('koperasis')
                ->onDelete('cascade');
            $table->foreign('id_pembelian')
                ->references('id')
                ->on('catatan_belis')
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
        Schema::dropIfExists('uang_kas');
    }
}
