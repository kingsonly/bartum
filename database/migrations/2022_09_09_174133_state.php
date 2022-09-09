<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class State extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('state', function (Blueprint $table) {
            $table->increments('stateid')->first();
            $table->timestamps();
            $table->string('prefix');
            $table->integer('sname');
            $table->integer('scapital')->default(0);
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
        Schema::dropIfExists('state');
    }
}
