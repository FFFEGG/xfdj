<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyMsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sy_msgs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('u_id');
            $table->foreign('u_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('msg');
            $table->dateTime('time');
            $table->decimal('sy');
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
        Schema::dropIfExists('sy_msgs');
    }
}
