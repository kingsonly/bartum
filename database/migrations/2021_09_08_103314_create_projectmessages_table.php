<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectmessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projectmessages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('projectid');
            $table->text('project');
            $table->string('clientuserid')->comment('id of the user in the users table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projectmessages');
    }
}
