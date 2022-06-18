<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('productname');
            $table->integer('numberofpanels');
            $table->integer('numberofbatteries');
            $table->text('description')->nullable();
            $table->decimal('price',12,2);
            $table->integer('trashed')->default(0)->comment('0 for not trashed, 1 for trashed');
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
        Schema::dropIfExists('products');
    }
}
