<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CometCompletion extends Model
{
    protected $table = 'comet_completions';

    protected $fillable = [
      'email',
      'last_name',
      'first_name',
      'supervisor',
      'org',
      'suborg_1',
      'suborg_2',
      'city',
      'state',
      'topic',
      'module',
      'language',
      'score',
      'date_completed'
    ];

    protected $casts = [
      'date_completed' => 'date'
    ];
}
