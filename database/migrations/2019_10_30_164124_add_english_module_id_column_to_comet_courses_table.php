<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnglishModuleIdColumnToCometCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comet_courses', function (Blueprint $table) {
            $table->unsignedInteger('english_module_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comet_courses', function (Blueprint $table) {
            $table->dropColumn('english_module_id');
        });
    }
}
