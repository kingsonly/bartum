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
            $table->integer('subitemid');
            $table->integer('userid');
            $table->integer('quantity')->default(0);
            $table->integer('itemid')->nullable();
            $table->string('tracking')->nullable();
            $table->string('transactiontype')->default('addition')->comment('addition or sold');
            $table->string('projecttid')->nullable();
            $table->string('price')->nullable();
            $table->string('name')->nullable();
            $table->string('capacity')->nullable();
            $table->string('status')->nullable();
            $table->string('rating')->nullable();
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
