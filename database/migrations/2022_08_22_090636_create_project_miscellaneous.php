<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectMiscellaneous extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_miscellaneous', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->integer('order_id');
            $table->integer('miscellaneous_id');
            $table->string('amount');
            $table->integer('status');
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_miscellaneous');
    }
}
