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
            $table->string('price')->nullable();
            $table->string('addedby')->nullable();
            $table->integer('numberofinverters')->nullable();
            $table->integer('batterytypeid')->nullable();
            $table->integer('invertertypeid')->nullable();
            $table->integer('solarpaneltypeid')->nullable();
            $table->string('account_name')->nullable();
            $table->string('mode_of_payment')->nullable();
            $table->string('discount_value')->nullable();
            $table->string('actual_amount')->nullable();
            $table->string('account_number')->nullable();
            $table->string('banks')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_duration')->nullable();
            $table->timestamp('deleted_at')->nullable();


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
