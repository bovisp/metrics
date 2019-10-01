<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCometCompletionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comet_completions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('org')->nullable();
            $table->string('suborg_1')->nullable();
            $table->string('suborg_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('topic')->nullable();
            $table->string('module')->nullable();
            $table->string('language')->nullable();
            $table->string('score')->nullable();
            $table->date('date_completed')->nullable();
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
        Schema::dropIfExists('comet_completions');
    }
}
