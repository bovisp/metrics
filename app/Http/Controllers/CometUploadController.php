<?php

namespace App\Http\Controllers;

use App\Classes\PersistCometFiles;
use Illuminate\Support\Facades\Storage;

class CometUploadController extends Controller
{
    public function store() {
      return (new PersistCometFiles(request('files')))->persist();
    }

    public function show() {
      return view('comet.uploads');
    }

    public function meta() {
      return uniqid(true);
    }

    public function upload() {
      request()->validate([
        'file' => 'required|file|mimes:xml'
      ]);

      $upload = request()->file('file');

      $extension = strtolower(
        explode('.', request()->file('file')->getClientOriginalName())[1]
      );

      Storage::putFileAs('/comet', $upload, request('id') . '.' . $extension);

      return [
        'codedFilename' => request('id') . '.' . $upload->getClientOriginalExtension(),
        'actualFilename' => request()->file('file')->getClientOriginalName()
      ];
    }
}
