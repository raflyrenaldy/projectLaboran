<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermintaanAplikasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permintaan_aplikasis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_ruangan');
            $table->unsignedInteger('id_thnajaran');
            $table->string('name');
            $table->string('name_dosen');
            $table->string('status')->default('new')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamps();
            $table->foreign('id_thnajaran')
                ->references('id')
                ->on('tahun_ajarans')
                ->onDelete('cascade');
            $table->foreign('id_user')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('permintaan_aplikasis');
    }
}
