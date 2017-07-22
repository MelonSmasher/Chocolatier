<?php
/**
 * Created by PhpStorm.
 * User: melon
 * Date: 7/21/17
 * Time: 7:17 PM
 */


Route::group(['middleware' => ['auth.nuget', 'file.nuget:package']], function () {
    Route::put('/upload', ['as' => 'upload', 'uses' => 'ApiController@upload']);
    Route::put('/', ['as' => 'upload', 'uses' => 'ApiController@upload']);
});

Route::get('/download/{id}/{version}', ['as' => 'download', 'uses' => 'ApiController@download']);

Route::group(['prefix' => '/api/v2'], function () {
    Route::group(['middleware' => ['auth.nuget', 'file.nuget:package']], function () {
        Route::put('/upload', ['as' => 'upload', 'uses' => 'ApiController@upload']);
        Route::put('/', ['as' => 'upload', 'uses' => 'ApiController@upload']);
        Route::put('/package', ['as' => 'upload', 'uses' => 'ApiController@upload']);
    });

    Route::get('/', ['as' => 'index', 'uses' => 'ApiController@index']);
    Route::get('$metadata', ['as' => 'metadata', 'uses' => 'ApiController@metadata']);
    Route::get('Packages()', ['as' => 'packages', 'uses' => 'ApiController@packages']);
    Route::get('Packages', ['as' => 'packages', 'uses' => 'ApiController@packages']);
    Route::get('GetUpdates()', ['as' => 'updates', 'uses' => 'ApiController@updates']);
    Route::get('GetUpdates', ['as' => 'updates', 'uses' => 'ApiController@updates']);
    Route::get('Search()/{action}', ['as' => 'search.action', 'uses' => 'ApiController@search']);
    Route::get('Search()', ['as' => 'search', 'uses' => 'ApiController@searchNoAction']);
    Route::get('Search', ['as' => 'search', 'uses' => 'ApiController@searchNoAction']);
    Route::get('FindPackagesById()', ['as' => 'findById', 'uses' => 'ApiController@packages']);
    Route::get('Packages(Id=\'{id}\',Version=\'{version}\')', ['as' => 'package', 'uses' => 'ApiController@package']);
});
