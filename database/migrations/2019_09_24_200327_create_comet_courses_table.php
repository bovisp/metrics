<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCometCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comet_courses', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedInteger('module_id');
          $table->string('title');
          $table->date('publish_date');
          $table->date('last_updated_on')->nullable();
          $table->string('completion_time')->nullable();
          $table->string('skill_level')->nullable();
          $table->string('image_src');
          $table->mediumText('description');
          $table->mediumText('topics');
          $table->mediumText('objectives');
          $table->mediumText('keywords');
          $table->boolean('include_in_catalog')->default(true);
          $table->boolean('msc_funded')->default(false);
          $table->unsignedInteger('language_id');
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
        Schema::dropIfExists('comet_courses');
    }
}
