<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});




/*
Route::get('/todos',function(){
	return \App\Todo::all();
});
*/

/*
// 有効にしている書き方
// Route::get('/todos', function(Request $request) {
// はもちろんOKだが、
// このコメントアウトしている書き方でもOKだった。
Route::get('/todos', function() {
	$user_id = request()->user_id;
	return \App\Todo::where('user_id', $user_id)->get();
});
*/

Route::get('/todos', function(Request $request) {
	// ここで\Auth::user()->idを使うとエラーになる。
	// web.phpのRoute::get('/todos', function(){内では大丈夫だったので、
	// ここで\Auth::user()->idを使うには何かが足りないのだろうか？
	// $auth_user_id = \Auth::user()->id;

	$user_id = $request->user_id;
	return \App\Todo::where('user_id', $user_id)->get();
});




Route::post('/todos',function(){

	$todo = \App\Todo::create(request()->all());
	
	event((new \App\Events\TodoAdded($todo))->dontBroadcastToCurrentUser());

	return $todo;
	
});




Route::get('/color-rects', function(){
	return \App\ColorRect::all();
});




/*
// もう一方の書き方
// Route::put('/color-rects', function(Request $request) {
// はもちろんOKだが、
// このコメントアウトしている書き方でもOKだった。
//
// 引数が
// axios.put(window.laravel.asset + '/api/color-rects', {
//     id: id,
//     mountPos: mountPos,
// })
// だった頃の書き方
Route::put('/color-rects', function() {
	$colorRect = \App\ColorRect::findOrFail(request()->id);

	if ($colorRect) {
		$colorRect->pos_top  = request()->mountPos['y'];
		$colorRect->pos_left = request()->mountPos['x'];

		// データベースに保存する
    	$colorRect->save();
	}
});
*/

/*
// この書き方もOK。
//
// 引数が
// axios.put(window.laravel.asset + '/api/color-rects', {
//     id: id,
//     mountPos: mountPos,
// })
// だった頃の書き方
Route::put('/color-rects', function(Request $request) {
	$colorRect = \App\ColorRect::findOrFail($request->id);

	if ($colorRect) {
		$colorRect->pos_top  = $request->mountPos['y'];
		$colorRect->pos_left = $request->mountPos['x'];

		// データベースに保存する
    	$colorRect->save();
	}
});
*/

Route::put('/color-rects', function(Request $request) {
	$colorRect = \App\ColorRect::findOrFail($request->updateColorRectParam['id']);

	if ($colorRect) {
		$colorRect->pos_top  = $request->updateColorRectParam['mountPos']['y'];
		$colorRect->pos_left = $request->updateColorRectParam['mountPos']['x'];

		// データベースに保存する
    	$colorRect->save();
	}
});

