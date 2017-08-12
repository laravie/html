<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class CreateHtmlModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('models', function ($table) {
            $table->increments('id');
            $table->string('string');
            $table->string('email');
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
        Schema::drop('models');
    }
}
