<?php
/**
 * Created by PhpStorm.
 * User: melon
 * Date: 7/21/17
 * Time: 7:17 PM
 */



Auth::routes();

Route::get('/', ['as' => 'home', 'uses' => 'GalleryController@home']);
Route::get('/browse', ['as' => 'packages.index', 'uses' => 'GalleryController@index']);
Route::resource('/packages', 'GalleryController');
Route::get('/packages/{name}', 'GalleryController');