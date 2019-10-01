<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCometViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comet_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->nullable();
            $table->string('last')->nullable();
            $table->string('first')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('org')->nullable();
            $table->string('suborg_1')->nullable();
            $table->string('suborg_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('topic')->nullable();
            $table->string('module')->nullable();
            $table->string('language')->nullable();
            $table->string('sessions')->nullable();
            $table->string('elapsed_time')->nullable();
            $table->string('session_pages')->nullable();
            $table->date('date')->nullable();
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
        Schema::dropIfExists('comet_views');
    }
}
