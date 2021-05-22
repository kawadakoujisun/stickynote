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
    // return view('welcome');
    return view('TaskReciever');
});

Route::get('/tasks', function () {
    $task = ['id' => 1, 'name' => 'メールの確認'];
    event(new \App\Events\TaskAdded($task));
});
