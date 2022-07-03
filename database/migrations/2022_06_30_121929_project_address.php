<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('project_address', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('projects_id');
            $table->string('client_id');
            $table->string('log');
            $table->string('lat');
            $table->string('address_description');
            $table->string('states_id');
            $table->string('lgas_id');
            $table->integer('status');
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
        Schema::dropIfExists('project_address');
    }
}
