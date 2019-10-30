<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('公司名称');
            $table->string('tel')->comment('联系人电话');
            $table->longText('file')->comment('文件');
            $table->integer('type')->comment('供应商类型');
            $table->string('username')->comment('供应商账号');
            $table->string('password')->comment('供应商密码');
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
        Schema::dropIfExists('gys');
    }
}
