<?php

Route::get('/', function() {
  return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/comet/uploads', 'CometUploadController@show');

Route::post('/comet/uploads/meta', 'CometUploadController@meta');

Route::post('/comet/uploads', 'CometUploadController@upload');