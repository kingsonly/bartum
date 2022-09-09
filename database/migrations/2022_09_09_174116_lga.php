<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Lga extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('lga', function (Blueprint $table) {
            $table->increments('lgaid')->first();
            $table->timestamps();
            $table->string('stateid');
            $table->integer('lganame');
            $table->integer('lsecretariate')->default(0);
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('lga');
    }
}
