<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CometCourse extends Model
{
    protected $table = 'comet_courses';

    protected $fillable = [
      'title',
      'publish_date',
      'skill_level',
      'completion_time',
      'topics',
      'last_updated_on',
      'description',
      'objectives',
      'keywords',
      'language_id',
      'module_id',
      'image_src'
    ];
}
