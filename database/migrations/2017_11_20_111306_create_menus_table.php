<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pid')->unsigned()->comment('父级ID');
            $table->string('url', 255)->default('')->comment('URL');
            $table->char('name', 50)->comment('节点名称');
            $table->string('path')->default('')->comment('菜单的层级关系');
            $table->integer('sort')->unsigned()->comment('排序');
            $table->tinyInteger('deep')->default(0)->comment('菜单层级');
            $table->integer('root_id')->default(0)->comment('根目录');
            $table->char('ico', 20)->default('')->comment('菜单图标');
            $table->tinyInteger('show')->default(1)->comment('是否显示');
            $table->char('mark', 255)->default('')->comment('备注');
            $table->timestamps();
            $table->index(['pid', 'path']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('menus');
    }
}
