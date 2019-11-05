<?php

namespace App\Classes;

use App\CometView;
use Carbon\Carbon;
use App\CometCompletion;
use Illuminate\Support\Facades\Cache;

class CometReport
{
  protected $startDate;

  protected $endDate;

  protected $cometCourses;

  protected $englishLangId;

  protected $frenchLangId;

  public function __construct(Carbon $startDate, Carbon $endDate)
  {
    $this->startDate = $startDate;

    $this->endDate = $endDate;

    $this->cometCourses = Cache::get('cometModules');

    $this->englishLangId = Cache::get('cometLanguages')->where('language', 'English')->first()->id;

    $this->FrenchLangId = Cache::get('cometLanguages')->where('language', 'French')->first()->id;
  }

  public function process() 
  {
    $viewsResults = $this->getResults(
      $this->getGroupedQuery(CometView::class, 'date'),
      CometView::class,
      'views'
    );

    $completionsResults = $this->getResults(
      $this->getGroupedQuery(CometCompletion::class, 'date_completed'),
      CometCompletion::class,
      'completions'
    );

    return [
      'views' => $viewsResults,
      'completions' => $completionsResults
    ];
  }

  protected function getGroupedQuery($model, $dateColumn)
  {
   return $model::whereBetween(
      $dateColumn, [$this->startDate->format('Y-m-d'), $this->endDate->format('Y-m-d')]
    )
      ->get()
      ->groupBy(['module']);
  }

  protected function getResults($query, $model, $type)
  {
    $dataKeys = $query->keys();

    $results = [];

    for ($i = 0; $i < count($dataKeys); $i++) {
      if (!$this->cometCourses->where('title', $dataKeys[$i])->where('language_id', $this->englishLangId)->first()) continue;

      $frenchCourse = $this->getFrenchCourse($dataKeys[$i]);

      $results[$i]['english_module_name'] = $dataKeys[$i];

      $results[$i]['french_module_name'] = $frenchCourse;

      $results[$i]['english_module_' . $type] = $this->getModuleStats($query[$dataKeys[$i]], $model);

      $results[$i]['french_module_' . $type] = $this->getFrenchModuleStats($query, $frenchCourse, $model);

      $results[$i]['other_module_' . $type] = $this->getOtherLanguageModuleStats($query, $dataKeys[$i], $model);

      $results[$i]['total_module_' . $type] = $this->getTotalModuleStats($results[$i], $type);
    }

    usort($results, function($a, $b) use ($type) {
      return $b['total_module_' . $type] <=> $a['total_module_' . $type];
    });

    return $results;
  }

  protected function getModuleStats($data, $model)
  {
    $model = explode("\\", $model)[1];

    if ($model === 'CometView') {
      return $data->sum('sessions');
    }

    return count($data);
  }

  protected function getTotalModuleStats($data, $type)
  {
    return $data['english_module_' . $type] +
           $data['french_module_' . $type] + 
           $data['other_module_' . $type];
  }

  protected function getFrenchCourse($moduleName)
  {
    $nonEnglishCourses = $this->getNonEnglishCourses($moduleName);

    if (!$nonEnglishCourses) return null;

    if ($nonEnglishCourses->where('language_id', $this->FrenchLangId)->first() === null) {
      return null;
    }

    return $nonEnglishCourses
      ->where('language_id', $this->FrenchLangId)
      ->first()
      ->title;
  }

  protected function getFrenchModuleStats($query, $frenchCourse, $model)
  {
    if (!$frenchCourse) return null;

    $hasFrenchStats = $query->has($frenchCourse);

    if (!$hasFrenchStats) return null;

    return $this->getModuleStats($query[$frenchCourse], $model);
  }

  protected function getOtherLanguageModuleStats($query, $moduleName, $model)
  {
    $nonEnglishCourses = $this->getNonEnglishCourses($moduleName);

    if (!$nonEnglishCourses) return null;

    $stats = 0;

    foreach ($nonEnglishCourses as $course) {
      if (!isset($query[$course->title])) continue;

      if ($course->language_id === 2) continue;

      $stats += $this->getModuleStats($query[$course->title], $model);
    }

    if ($stats === 0) return null;

    return $stats;
  }

  protected function getNonEnglishCourses($moduleName)
  {
    return optional(
      $this->cometCourses->where('title', $moduleName)->first()
    )->nonEnglishCourses;
  }
}