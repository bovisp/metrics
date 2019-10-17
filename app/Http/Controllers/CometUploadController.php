<?php

namespace App\Http\Controllers;

use App\CometView;
use App\CometCourse;
use App\CometCompletion;
use App\Classes\PersistCometFiles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CometUploadController extends Controller
{
    public function store() 
    {
      return [
        'nonMatchedCourses' => (new PersistCometFiles(request('files')))->persist(),
        'courseTitles' => CometCourse::select('title')->get()
      ];
    }

    public function storeCorrections()
    {
      foreach(request('completions') as $completion) {
        CometCompletion::create([
          'email' => $completion[0],
          'last_name' => $completion[1],
          'first_name' => $completion[2],
          'supervisor' => $completion[3],
          'org' => $completion[4],
          'suborg_1' => $completion[5],
          'suborg_2' => $completion[6],
          'city' => $completion[7],
          'state' => $completion[8],
          'topic' => $completion[9],
          'module' => $completion[10],
          'language' => $completion[11],
          'score' => $completion[12],
          'date_completed' => date('Y-m-d', strtotime($completion[13]))
        ]);

        $correctionExists = DB::table('corrected_comet_titles')
          ->where('incorrect_title', $completion[count($completion) - 1])
          ->first();

        $course = CometCourse::where('title', $completion[10])->firstOrFail();

        if ($correctionExists === null) {
          DB::table('corrected_comet_titles')->insert([
            'comet_course_id' => $course->id,
            'incorrect_title' => $completion[count($completion) - 1]
          ]);
        }
      }

      foreach(request('views') as $view) 
      {
        CometView::create([
          'email' => $view[0],
          'last' => $view[1],
          'first' => $view[2],
          'supervisor' => $view[3],
          'org' => $view[4],
          'suborg_1' => $view[5],
          'suborg_2' => $view[6],
          'city' => $view[7],
          'state' => $view[8],
          'topic' => $view[9],
          'module' => $view[10],
          'language' => $view[11],
          'sessions' => $view[12],
          'elapsed_time' => $view[13],
          'session_pages' => $view[14],
          'date' => date('Y-m-d', strtotime($view[15]))
        ]);

        $correctionExists = DB::table('corrected_comet_titles')
          ->where('incorrect_title', $view[count($view) - 1])
          ->first();

        $course = CometCourse::where('title', $view[10])->firstOrFail();

        if ($correctionExists === null) {
          DB::table('corrected_comet_titles')->insert([
            'comet_course_id' => $course->id,
            'incorrect_title' => $view[count($view) - 1]
          ]);
        }
      }

      return response([
        'message' => 'success'
      ], 200);
    }

    public function show() 
    {
      return view('comet.uploads');
    }

    public function meta() 
    {
      return uniqid(true);
    }

    public function upload() 
    {
      request()->validate([
        'file' => 'required|file|mimes:xml'
      ]);

      $upload = request()->file('file');

      $extension = strtolower(
        explode('.', request()->file('file')->getClientOriginalName())[1]
      );

      Storage::putFileAs('/public/comet', $upload, request('id') . '.' . $extension);

      return [
        'codedFilename' => request('id') . '.' . $upload->getClientOriginalExtension(),
        'actualFilename' => request()->file('file')->getClientOriginalName()
      ];
    }
}
