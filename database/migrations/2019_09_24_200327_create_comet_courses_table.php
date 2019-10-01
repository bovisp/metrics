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
            $table->string('title');
            $table->date('publish_date');
            $table->date('last_updated')->nullable();
            $table->string('completion_time');
            $table->string('image_src');
            $table->mediumText('description');
            $table->string('topics');
            $table->string('url');
            $table->boolean('include_in_catalog')->default(true);
            $table->boolean('msc_funded')->default(false);
            $table->unsignedInteger('language_id');
            $table->unsignedInteger('english_version_id')->nullable();
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
