<?php

namespace App\Http\Controllers;

use App\CometCourse;
use App\Classes\CometReport;

class ProcessCometStatsController extends Controller
{
    public function index()
    {
      return (new CometReport('january 1 2015', 'september 1 2019'))->process();
    }
}
