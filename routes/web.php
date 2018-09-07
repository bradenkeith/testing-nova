<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware('signed', 'emailaccessesproject')->group(function () {
    Route::get('/projects/{project}/{email_address}', 'ProjectController@show')
        ->name('projects');

    Route::post('/returned-file/{project}/{email_address}', 'ProjectController@returnedFileUpload')
        ->name('returned-file');

    Route::get('/download/{project}/{email_address}/{file_path}', 'ProjectController@download')
        ->name('download');
});
