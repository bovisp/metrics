<?php

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

Route::get('/', function() {
  return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('comet')->middleware('auth')->group(function() {

  Route::get('/uploads', 'CometUploadController@show');

  Route::post('/uploads/meta', 'CometUploadController@meta');

  Route::post('/uploads', 'CometUploadController@upload');

  Route::post('/uploads/store', 'CometUploadController@store');

  Route::post('/corrections', 'CometUploadController@storeCorrections');
});

Route::prefix('/api/users')->middleware('auth')->group(function() {
  Route::get('/', 'Api\UsersController@index');

  Route::post('/', 'Api\UsersController@invite');

  Route::put('/{user}/profile', 'Api\UsersController@updateProfile');

  Route::put('/{user}/password', 'Api\UsersController@updatePassword');

  Route::delete('/{user}', 'Api\UsersController@destroy');
});

Route::get('/users', 'UsersController@index')
  ->middleware('auth');

Route::get('/users/register', 'UsersController@register')
  ->middleware('invitetoken');

Route::get('/scrape', 'CometScrapeController@scrape');