<?php
/**
 * Created by PhpStorm.
 * User: melon
 * Date: 7/21/17
 * Time: 7:17 PM
 */


Auth::routes();

Route::get('/', 'GalleryController@home')->name('home');
Route::get('/browse', 'GalleryController@browse')->name('packages.index');
Route::get('/packages', 'GalleryController@index')->name('gallery.index');
Route::get('/packages/{name}', 'GalleryController@showPackage')->name('gallery.show.package');