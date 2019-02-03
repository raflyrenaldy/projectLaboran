<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatatanBelisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catatan_belis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_user')->nullable();
            $table->string('name');
            $table->bigInteger('harga');
            $table->integer('jumlah');
            $table->string('status')->default('new')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('catatan_belis');
    }
}
