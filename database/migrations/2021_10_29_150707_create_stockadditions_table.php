<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockadditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stockadditions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('subitemid')->nullable();
            $table->integer('userid')->nullable();
            $table->integer('itemid')->nullable();
            $table->string('price')->nullable();
            $table->string('name')->nullable();
            $table->string('capacity')->nullable();
            $table->string('status')->nullable();
            $table->string('rating')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('stockid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stockadditions');
    }
}
