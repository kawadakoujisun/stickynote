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


//　認証なし
Route::get('/', function () {
    return view('read.index');
});


// 認証付きのルーティング
Route::group(['middleware' => ['auth']], function () {

    /*
    Route::get('/', function () {
        // return view('welcome');
        // return view('TaskReciever');
        return view('index');
    });
    */
    
    Route::get('/tasks', function () {
        print 'tasks';
        $task = ['id' => 1, 'name' => 'メールの確認'];
        event(new \App\Events\TaskAdded($task));
    });

    Route::get('/todos', function(){
        // ここで\Auth::user()->idを使うのはエラーにはならない。
        // $auth_user_id = \Auth::user()->id;
        // dd($auth_user_id);
        
    	return view('todos.index');
    });
    
    Route::get('/color-rects', function(){
    	return view('color-rects.index');
    });

    Route::get('/work', function(){
    	return view('work.index');
    });
        
});