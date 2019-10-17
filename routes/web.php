<?php

Route::get('/', function() {
  return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/comet/uploads', 'CometUploadController@show');

Route::post('/comet/uploads/meta', 'CometUploadController@meta');

Route::post('/comet/uploads', 'CometUploadController@upload');

Route::post('/comet/uploads/store', 'CometUploadController@store');

Route::post('/comet/corrections', 'CometUploadController@storeCorrections');

Route::get('/users', 'UsersController@index');

Route::get('/users/register', 'UsersController@register');