<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelWajahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_wajah', function (Blueprint $table) {
            $table->id();
            $table->string('npk_karyawan', 16)->index();
            $table->string('key')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();

            $table->foreign('npk_karyawan')->references('npk')->on('karyawan')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_wajah');
    }
}
