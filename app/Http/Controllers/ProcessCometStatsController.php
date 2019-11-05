<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\ProcessCometStats;

class ProcessCometStatsController extends Controller
{
  public function index()
  {
    return (new ProcessCometStats(Carbon::now()->submonths(48), Carbon::now(), true))->handle();
  }
}
