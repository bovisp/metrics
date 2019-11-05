<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Jobs\ProcessCometStats;
use App\Http\Controllers\Controller;

class ChartsController extends Controller
{
  public function index()
  {
    return (new ProcessCometStats(Carbon::now()->submonths(48), Carbon::now(), false))->handle();
  }
}
