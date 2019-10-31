<?php

namespace App\Providers;

use App\CometCourse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Cache::forever('cometLanguages', DB::table('comet_languages')->get());

      Cache::forever('cometModules', CometCourse::with('nonEnglishCourses')->whereNull('english_module_id')->get());
    }
}
