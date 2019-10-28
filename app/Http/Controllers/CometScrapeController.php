<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CometScrapeController extends Controller
{
    public function index()
    {
      return view('comet.scrape');
    }
}
