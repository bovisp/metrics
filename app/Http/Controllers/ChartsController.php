<?php

namespace App\Http\Controllers;

class ChartsController extends Controller
{
    public function index()
    {
      return view('charts.index');
    }
}
