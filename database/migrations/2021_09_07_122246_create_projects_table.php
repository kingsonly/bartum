<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // please make sure to add other new fields 
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('projectname')->nullable();
            $table->string('projecttype')->nullable();
            $table->string('solarsystemsize')->nullable();
            $table->string('numberofpanels')->nullable();
            $table->string('numberofbatteries')->nullable();
            $table->string('description')->nullable();
            $table->string('productid')->nullable();
            $table->string('installationtype')->nullable();
            $table->string('status')->nullable();
            $table->string('clientid')->nullable()->comment('the id in the client table');
            $table->string('clientuserid')->nullable()->comment('the userid in the client table,  this is the same as the id in the user table');
            $table->string('projectcode')->nullable();
            $table->string('lgaid')->nullable();
            $table->string('lga')->nullable();
            $table->string('trashed')->nullable();
            $table->string('stateid')->nullable();
            $table->decimal('price',12,2)->nullable();
            $table->string('addedby')->nullable();
            $table->integer('numberofinverters')->nullable();
            $table->integer('batterytypeid')->nullable();
            $table->integer('invertertypeid')->nullable();
            $table->integer('solarpaneltypeid')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
