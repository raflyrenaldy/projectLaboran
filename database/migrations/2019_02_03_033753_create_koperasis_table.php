<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKoperasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('koperasis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_user')->nullable();
            $table->unsignedInteger('id_user_pinjam')->nullable();
            $table->string('keterangan')->nullable();
            $table->bigInteger('jumlah');
            $table->string('status')->default('new')->nullable();
            $table->timestamps();
            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('id_user_pinjam')
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
        Schema::dropIfExists('koperasis');
    }
}
