<?php

use Illuminate\Http\Request;

use App\ProjectWork\ImageUtil;  // 追加

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
    	
    	// イベント
    	$colorRectUpdateParam = [
    		'id'       => $colorRect->id,
    		'pos_top'  => $colorRect->pos_top,
    		'pos_left' => $colorRect->pos_left,
    		'user_id'  => $request->user_id,
    	];
    	
    	event((new \App\Events\ColorRectUpdate($colorRectUpdateParam))->dontBroadcastToCurrentUser());
	}
});




Route::post('/work-sticker-create', function(Request $request) {
	// ふせんを作成し、データベースに保存する
	$sticker = \App\Sticker::createSticker();
	
	$stickerInfoItemPos   = $sticker->infoItemPos;
	$stickerInfoItemColor = $sticker->infoItemColor;
	
	// イベント
	$eventParam = [
		'id'       => $sticker->id,
	    'pos_top'  => $stickerInfoItemPos->pos_top,
	    'pos_left' => $stickerInfoItemPos->pos_left,
	    'color'    => $stickerInfoItemColor->color,
	    'user_id'  => $request->user_id,
	];
	
	event((new \App\Events\StickerCreate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
});

Route::delete('/work-sticker-destroy', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		// 画像ファイルの情報を取得し、削除する
		$stickerContentItemImages = $sticker->contentItemImages;
		if ($stickerContentItemImages) {
			foreach ($stickerContentItemImages as $contentItemImage) {
				$imagePublicId = $contentItemImage->image_public_id;
		
				// 画像ファイルを削除する
        		ImageUtil::destroyImage($imagePublicId);
			}
		}
		
		// ふせんを削除し、データベースに保存する
		$sticker->delete();
	    
	    // イベント
	    $eventParam = [
	    	'id'      => $sticker->id,
	    	'user_id' => $request->user_id,
	    ];
	    
	    event((new \App\Events\StickerDestroy($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
	}
});

Route::get('/work-mount', function() {
	$stickerParams = array();

    $stickers = \App\Sticker::all();
    foreach ($stickers as $sticker) {
		$stickerInfoItemPos      = $sticker->infoItemPos;
		$stickerInfoItemColor    = $sticker->infoItemColor;
		$stickerContentLinks     = $sticker->contentLinks;
		$stickerContentItemTexts = $sticker->contentItemTexts;
		$stickerContentItemImages = $sticker->contentItemImages;
		
		if ($stickerInfoItemPos && $stickerInfoItemColor) {
			$stickerParam = [
				'id'       => $sticker->id,
				'pos_top'  => $stickerInfoItemPos->pos_top,
				'pos_left' => $stickerInfoItemPos->pos_left,
				'color'    => $stickerInfoItemColor->color,
				// 'contents' => array(),  // この後要素数0であっても必ず配列を設定します。
			];
			
			$contents = array();
			
			if ($stickerContentLinks) {
				foreach ($stickerContentLinks as $contentLink) {
					$item_type = $contentLink->item_type;
					$item_id   = $contentLink->item_id;
					
					$link = [
						'id'        => $contentLink->id,
						'item_type' => $item_type,
						'item_id'   => $item_id,
					];
					
					$content = [
						'link' => $link,
						// 'item' => $item,  // この後'item'を設定します。
					];
					
					if ($item_type == \App\Sticker::$contentItemType['text']) {
						if ($stickerContentItemTexts) {
							$contentItemText = $stickerContentItemTexts->where('id', $item_id)->first();
							if ($contentItemText) {
								$text = $contentItemText->text;
								if ($text) {
									$item = [
										'text' => $text,
									];
									
									$content['item'] = $item;

							        array_push($contents, $content);
								}
							}
						}
					} else if ($item_type == \App\Sticker::$contentItemType['image']) {
						if ($stickerContentItemImages) {
							$contentItemImage = $stickerContentItemImages->where('id', $item_id)->first();
							if ($contentItemImage) {
								$imageURL      = $contentItemImage->image_url;
								$imagePublicId = $contentItemImage->image_public_id;
								
								$item = [
								    'image_url'       => $imageURL,
								    'image_public_id' => $imagePublicId,
								];
								
								$content['item'] = $item;
								
							    array_push($contents, $content);
							}
						}
					}
				}
			}
			
			$stickerParam['contents'] = $contents;
			
			array_push($stickerParams, $stickerParam);
		}
	}
	
	return $stickerParams;
});

Route::put('/work-sticker-info-item-pos-update', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$stickerInfoItemPos = $sticker->infoItemPos;
		if ($stickerInfoItemPos) {
			$stickerInfoItemPos->pos_top  = $request->reqParam['mountPos']['y'];
			$stickerInfoItemPos->pos_left = $request->reqParam['mountPos']['x'];
	
			// データベースに保存する
	    	$stickerInfoItemPos->save();
	    	
	    	// イベント
	    	$eventParam = [
	    		'id'       => $sticker->id,
	    		'pos_top'  => $stickerInfoItemPos->pos_top,
	    		'pos_left' => $stickerInfoItemPos->pos_left,
	    		'user_id'  => $request->user_id,
	    	];
	    	
	    	event((new \App\Events\StickerInfoItemPosUpdate($eventParam))->dontBroadcastToCurrentUser());
		}
	}
});

Route::put('/work-sticker-info-item-color-update', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$stickerInfoItemColor = $sticker->infoItemColor;
		if ($stickerInfoItemColor) {
			$stickerInfoItemColor->color = $request->reqParam['color'];
	
			// データベースに保存する
	    	$stickerInfoItemColor->save();
	    	
	    	// イベント
	    	$eventParam = [
	    		'id'      => $sticker->id,
	    		'color'   => $stickerInfoItemColor->color,
	    		'user_id' => $request->user_id,
	    	];
	    	
	    	event((new \App\Events\StickerInfoItemColorUpdate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
		}
	}
});

Route::post('/work-sticker-content-item-text-create', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$text = $request->reqParam['text'];
		
		// テキストを作成し、データベースに保存する
		list($contentLink, $contentItem) = $sticker->createContentItemText([
		    'text' => $text,
		]);
	    
	    // イベント
	    $eventParam = [
	    	'id'                => $sticker->id,
	    	'content_link_id'   => $contentLink->id,
	    	'content_item_type' => $contentLink->item_type,
	    	'content_item_id'   => $contentLink->item_id,
	    	'text'              => $text,
	    	'user_id'           => $request->user_id,
	    ];
	    
	    event((new \App\Events\StickerContentItemTextCreate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
	}
});

Route::delete('/work-sticker-content-item-text-destroy', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$content_link_id = $request->reqParam['content_link_id'];
		
		// テキストを削除し、データベースに保存する
		$sticker->destroyContentItem([
		    'content_link_id' => $content_link_id,
		]);
	    
	    // イベント
	    $eventParam = [
	    	'id'              => $sticker->id,
	    	'content_link_id' => $content_link_id,
	    	'user_id'         => $request->user_id,
	    ];
	    
	    event((new \App\Events\StickerContentItemTextDestroy($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
	}
});

Route::post('/work-sticker-content-item-image-create', function(Request $request) {
	// Log::debug('my_debug_log: '. 'Route::post /work-sticker-content-item-image-create');
	// logger($request);
	
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		// 画像ファイルをアップする
        list($imageURL, $imagePublicId) = ImageUtil::uploadImage($request->reqParam['selectImageFileInfo']);
        
        // ContentItemImageを作成し、データベースに保存する
		list($contentLink, $contentItem) = $sticker->createContentItemImage([
		    'image_url'       => $imageURL,
		    'image_public_id' => $imagePublicId,
		]);
		
		// イベント
	    $eventParam = [
	    	'id'                => $sticker->id,
	    	'content_link_id'   => $contentLink->id,
	    	'content_item_type' => $contentLink->item_type,
	    	'content_item_id'   => $contentLink->item_id,
		    'image_url'         => $imageURL,
		    'image_public_id'   => $imagePublicId,
	    	'user_id'           => $request->user_id,
	    ];
		
		//event((new \App\Events\StickerContentItemImageCreate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
	}
});

Route::delete('/work-sticker-content-item-image-destroy', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$content_link_id = $request->reqParam['content_link_id'];
		
		// 画像ファイルの情報を取得する
		$stickerContentLinks      = $sticker->contentLinks;
		$stickerContentItemImages = $sticker->contentItemImages;
		$contentLink      = $stickerContentLinks->where('id', $content_link_id)->first();
		$contentItemImage = $stickerContentItemImages->where('id', $contentLink->item_id)->first();
		$imagePublicId = $contentItemImage->image_public_id;
		
		// 画像ファイルを削除する
        ImageUtil::destroyImage($imagePublicId);
		
		// ContentItemImageを削除し、データベースに保存する
		$sticker->destroyContentItem([
		    'content_link_id' => $content_link_id,
		]);
	    
	    // イベント
	    $eventParam = [
	    	'id'              => $sticker->id,
	    	'content_link_id' => $content_link_id,
	    	'user_id'         => $request->user_id,
	    ];
	    
	    //event((new \App\Events\StickerContentItemImageDestroy($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
	}
});
