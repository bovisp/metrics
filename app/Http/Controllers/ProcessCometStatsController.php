<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\CometCourse;
use App\Mail\CometStats;
use App\Classes\CometReport;
use App\Exports\CometExport;
use App\Jobs\ProcessCometStats;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ProcessCometStatsController extends Controller
{
    public function index()
    {
      $start = 'january 1 2015';

      $end = 'september 1 2019';

      $stats = (new CometReport($start, $end))->process();

      $filename = 'comet_stats_' . (new Carbon($start))->format('Ymd') . '_' . (new Carbon($end))->format('Ymd') . '.xlsx';

      Excel::store(new CometExport($stats), $filename);

      Mail::to(request()->user())->send(new CometStats($start, $end, $filename));

      Storage::delete($filename);
    }
}
