<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gys', function (Blueprint $table) {
            //
            $table->integer('status')->default(0)->comment('0=>审核中，1=>正常，-1=>冻结');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gys', function (Blueprint $table) {
            //
        });
    }
}
