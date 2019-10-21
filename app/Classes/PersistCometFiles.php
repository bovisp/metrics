<?php

namespace App\Classes;

ini_set('max_execution_time', '600');

use App\CometView;
use App\CometCourse;
use App\CometCompletion;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Mtownsend\XmlToArray\XmlToArray;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PersistCometFiles
{
  /**
   * Default directory where the uploaded COMET files are stored
   *
   * @var string
   */
  protected $cometDir;

  /**
   * An array of COMET files
   *
   * @var array
   */
  protected $files;

  protected $nonMatchedCourses = [
    'completions' => [],
    'views' => []
  ];

  public function __construct($files)
  {
    $this->cometDir = storage_path() . '/app/public/comet/';

    $this->files = $files;

    $this->cometCompletions = CometCompletion::all();

    $this->cometViews = CometView::all();

    $this->cometCourses = CometCourse::all();
  }

  
  /**
   * Iterates through each incoming XML file and returns 
   * an array of entries whose module names do not match
   * an existing module name. This is usually due to 
   * database encoding difficulties.
   *
   * @return array
   */
  public function persist()
  {
    $this->iterateFiles();

    return $this->nonMatchedCourses;
  }

  /**
   * Iterates through the XML fies, cleans up the encoding
   * and converts the XML to an array and for further 
   * processing.
   *
   * @return void
   */
  protected function iterateFiles()
  {
    foreach($this->files as $file) {
      $content = File::get($this->cometDir . $file);

      // There are some encodng problems with some of the files
      // Have to do this first.
      $content = utf8_encode($content);

      // The & character isn't encoded properly.
      // Fixing that here.
      $content = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $content);

      // Deleting certain XML tags and converting the XML data to an array.
      $cleanFileContent = $this->cleanFile($content);

      // From the data in the array that was converted from XML above,
      // We now need to get the actual data we need.
      $completions = $this->cleanData($cleanFileContent, 0);

      $views = $this->cleanData($cleanFileContent, 1);

      $unmatchedCompletions = $this->persistCompletions($completions);

      // If no new COMET completion data is persisted, this is a duplicate file.
      // If data is persisted and there are module names that have been 
      // incorrectly encoded, add them to the non-matched courses array.
      if ((CometCompletion::all())->count() === $this->cometCompletions->count()) {
        continue;
      } else {
        foreach ($unmatchedCompletions as $completion) {
          $this->nonMatchedCourses['completions'][] = $completion;
        }
      }

      $unmatchedViews = $this->persistViews($views);

      // If no new COMET view data is persisted, this is a duplicate file.
      // If data is persisted and there are module names that have been 
      // incorrectly encoded, add them to the non-matched courses array.
      if ((CometView::all())->count() === $this->cometViews->count()) {
        continue;
      } else {
        foreach ($unmatchedViews as $view) {
          $this->nonMatchedCourses['views'][] = $view;
        }
      }

      File::delete($this->cometDir . $file);
    }
  }

  /**
   * Converts the XML to a collection and drops
   * unnecessary XML headers.
   *
   * @param string $content
   * @return Illuminate\Support\Collection
   */
  protected function cleanFile($content)
  {
    $array = XmlToArray::convert($content);

    $collection = collect($array);

    $collection->forget('DocumentProperties')
      ->forget('OfficeDocumentSettings')
      ->forget('ExcelWorkbook')
      ->forget('Styles');

    return $collection->has('Worksheet') ? 
      $collection['Worksheet'] : 
      $collection['ss:Worksheet'];
  }

  /**
   * Extracts the necessary data from a collection.
   *
   * @param Illuminate\Support\Collection $data
   * @param int $index
   * @return array
   */
  protected function cleanData($data, $index)
  {
    $cleanData = [];

    $rawData = Arr::has($data[$index], 'Table') ? 
      array_slice($data[$index]['Table']['Row'], 2) : 
      array_slice($data[$index]['ss:Table']['ss:Row'], 3);

    foreach($rawData as $item) {
      $cleanData[] = $this->mapRow($item);
    }

    return $cleanData;
  }

  /**
   * For each row of the data array, extract the
   * necessary data.
   *
   * @param array $item
   * @return array
   */
  protected function mapRow($item)
  {
    $cellText = Arr::has($item, 'Cell') ? 'Cell' : 'ss:Cell';

    return array_map(function($column) {
      $dataText = Arr::has($column, 'Data') ? 'Data' : 'ss:Data';

      return Arr::get($column, $dataText . '.@content');
    }, $item[$cellText]);
  }

  /**
   * For the completions, we check for encoding errors in the 
   * module name, then check if that record already exists,
   * then we persist to the database.
   *
   * @param array $data
   * @return void
   */
  protected function persistCompletions($data)
  {
    $nonMatchArray = [];

    foreach($data as $item) {
      if ($item[11] === 'Spanish') continue;

      if ($this->hasCorrectedTitle($item[10])) {
        $item[10] = CometCourse::find($this->hasCorrectedTitle($item[10]))->title;

        continue;
      } 

      if ($this->validateModuleName($item, 'completions') === null) {
        $nonMatchArray[] = $item;

        continue;
      }

      $exists = $this->cometCompletions
        ->where('email', $item[0])
        ->where('last_name', $item[1])
        ->where('first_name', $item[2])
        ->where('module', $item[10])
        ->where('score', $item[12])
        ->first();      

      if ($exists) continue;

      CometCompletion::create([
        'email' => $item[0],
        'last_name' => $item[1],
        'first_name' => $item[2],
        'supervisor' => $item[3],
        'org' => $item[4],
        'suborg_1' => $item[5],
        'suborg_2' => $item[6],
        'city' => $item[7],
        'state' => $item[8],
        'topic' => $item[9],
        'module' => $item[10],
        'language' => $item[11],
        'score' => $item[12],
        'date_completed' => date('Y-m-d', strtotime($item[13]))
      ]);
    }

    return $nonMatchArray;
  }

  /**
   * For the views, we check for encoding errors in the 
   * module name, then we persist to the database.
   *
   * @param array $data
   * @return void
   */
  protected function persistViews($data)
  {
    $nonMatchArray = [];

    foreach($data as $item) {
      if ($item[11] === 'Spanish') {
        continue;
      }
      
      if ($this->hasCorrectedTitle($item[10])) {
        $item[10] = CometCourse::find($this->hasCorrectedTitle($item[10]))->title;

        continue;
      } 
      
      if (is_null($this->validateModuleName($item, 'views'))) {
        $nonMatchArray[] = $item;

        continue;
      }

      CometView::create([
        'email' => $item[0],
        'last' => $item[1],
        'first' => $item[2],
        'supervisor' => $item[3],
        'org' => $item[4],
        'suborg_1' => $item[5],
        'suborg_2' => $item[6],
        'city' => $item[7],
        'state' => $item[8],
        'topic' => $item[9],
        'module' => $item[10],
        'language' => $item[11],
        'sessions' => $item[12],
        'elapsed_time' => $item[13],
        'session_pages' => $item[14],
        'date' => date('Y-m-d', strtotime($item[15]))
      ]);
    }

    return $nonMatchArray;
  }

  /**
   * Check to see if the module name for the new record 
   * matches an actual module name. If it comes sufficiently close
   * to an actual name, the actual name is assigned. Otherwise 
   * it is added to an non-matching module name array that the 
   * user will have to manually correct in the browser.
   *
   * @param array $item
   * @param string $type
   * @return void
   */
  protected function validateModuleName($item, $type) {
    foreach($this->cometCourses as $course) {
      $sim = similar_text(
        iconv('UTF-8','ASCII//TRANSLIT//IGNORE', $course->title), 
        iconv('UTF-8','ASCII//TRANSLIT//IGNORE', $item[10]), 
        $percent
      );

      if ($percent == 100) {
        return $course->title;
      } else if ($item[11] === 'English' && $percent >= 98) {
        return $course->title;
      } else if ($item[11] === 'French' && $percent >= 93) {
        return $course->title;
      }
    }

    return null;
  }

    /**
     * Check to see if the incorrect title exists in the 'corrected_comet_titles'
     * table. 
     *
     * @param int $title
     * @return int
     */
  protected function hasCorrectedTitle($title) {
    return optional(
      DB::table('corrected_comet_titles')
        ->where('incorrect_title', $title)
        ->first()
      )->comet_course_id;
  }
}