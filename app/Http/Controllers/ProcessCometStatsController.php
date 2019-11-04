<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\CometCourse;
use App\Classes\CometReport;
use App\Exports\CometExport;
use Maatwebsite\Excel\Facades\Excel;

class ProcessCometStatsController extends Controller
{
    public function index()
    {
      $stats = (new CometReport('january 1 2015', 'september 1 2019'))->process();

      $filename = 'comet_stats_' . (new Carbon('january 1 2015'))->format('Ymd') . '_' . (new Carbon('september 1 2019'))->format('Ymd') . '.xlsx';

      Excel::store(new CometExport($stats), $filename);
    }
}
