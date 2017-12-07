<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_api', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id', false, true);
            $table->integer('api_id', false, true);

            $table->index('menu_id');
            $table->index('api_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('menu_api');
    }
}
