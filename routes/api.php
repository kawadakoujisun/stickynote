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

Route::get('/todos',function(){
	return \App\Todo::all();
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
// 有効にしている書き方
// Route::put('/color-rects', function(Request $request) {
// はもちろんOKだが、
// このコメントアウトしている書き方でもOKだった。
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

Route::put('/color-rects', function(Request $request) {
	$colorRect = \App\ColorRect::findOrFail($request->id);

	if ($colorRect) {
		$colorRect->pos_top  = $request->mountPos['y'];
		$colorRect->pos_left = $request->mountPos['x'];

		// データベースに保存する
    	$colorRect->save();
	}
});
