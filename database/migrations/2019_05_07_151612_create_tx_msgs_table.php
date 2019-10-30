<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTxMsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tx_msgs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('u_id');
            $table->foreign('u_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('price');
            $table->string('name');
            $table->string('tel');
            $table->string('banknum');
            $table->string('khh');
            $table->string('khzh');
            $table->boolean('is_pass')->default(0);
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
        Schema::dropIfExists('tx_msgs');
    }
}
