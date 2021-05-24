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

// 認証
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.post');
Route::get('logout', 'Auth\LoginController@logout')->name('logout.get');


// 認証付きのルーティング
Route::group(['middleware' => ['auth']], function () {

    Route::get('/', function () {
        // return view('welcome');
        return view('TaskReciever');
    });
    
    Route::get('/tasks', function () {
        print 'tasks';
        $task = ['id' => 1, 'name' => 'メールの確認'];
        event(new \App\Events\TaskAdded($task));
    });

});