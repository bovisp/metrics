<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CometView extends Model
{
  protected $table = 'comet_views';

  protected $fillable = [
    'email',
    'last',
    'first',
    'supervisor',
    'org',
    'suborg_1',
    'suborg_2',
    'city',
    'state',
    'topic',
    'module',
    'language',
    'sessions',
    'elapsed_time',
    'session_pages',
    'date'
  ];

  protected $casts = [
    'date' => 'date'
  ];
}
