<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xs_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('u_id');
            $table->foreign('u_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('tel');
            $table->string('address');
            $table->string('img');
            $table->decimal('tc');
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
        Schema::dropIfExists('xs_users');
    }
}
