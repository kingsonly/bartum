<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditrailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auditrails', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('ip',100)->nullable();
            $table->string('useragent')->nullable();
            $table->string('email',100)->nullable();
            $table->string('time',100)->nullable();
            $table->string('action')->nullable();
            $table->longText('object')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auditrails');
    }
}
