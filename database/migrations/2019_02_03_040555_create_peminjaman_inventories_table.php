<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeminjamanInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peminjaman_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_user')->nullable();
            $table->unsignedInteger('id_user_pinjam')->nullable();
            $table->unsignedInteger('id_inventory')->nullable();
            $table->string('keterangan')->nullable();
            $table->bigInteger('jumlah');
            $table->string('status')->default('new')->nullable();
            $table->timestamps();
            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('id_inventory')
                ->references('id')
                ->on('inventories')
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
        Schema::dropIfExists('peminjaman_inventories');
    }
}
