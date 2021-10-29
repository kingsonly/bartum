<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('userid')->nullable();
            $table->string('title')->nullable();
            $table->string('message')->nullable();
            $table->string('table')->nullable();
            $table->string('date')->nullable();
            $table->string('idintable')->nullable();
            $table->string('seen')->default("NO")->comment("YES AND NO");
            $table->string('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
