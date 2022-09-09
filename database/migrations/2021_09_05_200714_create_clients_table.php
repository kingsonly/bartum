<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('clientname');
            $table->string('clienttype');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('load')->nullable();
            $table->string('housesize')->nullable();
            $table->string('address')->nullable();
            $table->string('cityid')->nullable();
            $table->string('city')->nullable();
            $table->string('stateid')->nullable();
            $table->string('state')->nullable();
            $table->integer('userid')->nullable();
            $table->string('clientcode')->nullable();
            $table->string('lgaid')->nullable();
            $table->string('lga')->nullable();
            $table->string('addedby')->nullable()->comment('userid of who added');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
