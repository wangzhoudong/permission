<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pid')->comment('父分类');
            $table->string('name')->comment('角色名字');
            $table->tinyInteger('level')->comment('角色等级');
            $table->string('path')->default('')->comment('菜单的层级关系');
            $table->integer('root_id')->default(0)->comment('根目录');
            $table->tinyInteger('deep')->default(0)->comment('角色等级');
            $table->string('mark')->default('')->comment('备注');
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
        Schema::dropIfExists('roles');
    }
}
