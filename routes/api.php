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




Route::post('/work-sticky-note-import', function(Request $request) {
	// 既存のふせんを全て削除し、データベースに保存する
	$stickers = \App\Sticker::all();
	foreach ($stickers as $sticker) {
		$sticker->destroySticker();
	}
	
	// ふせんを作る
	$dstStickerParams = array();
	
	$srcStickerParams = $request->reqParam['stickerParams'];
	foreach ($srcStickerParams as $srcStickerParam) {
		// ふせんを作成し、データベースに保存する
		$sticker = \App\Sticker::createSticker();
		
		$dstStickerParam = [
			'id' => $sticker->id,
			// 'individual_main_number' => ,
			// 'individual_sub_number'  => ,
			// 'pos_top'  => ,
			// 'pos_left' => ,
			// 'depth'    => ,
			// 'color'    => ,
			// 'contents' => array(),  // 要素数0であっても必ず配列を設定します。			
		];
		
		if ($sticker) {
			// infoItemIndividualNumber
			$stickerInfoItemIndividualNumber = $sticker->infoItemIndividualNumber;
			if ($stickerInfoItemIndividualNumber) {
				$stickerInfoItemIndividualNumber->main_number = $srcStickerParam['individual_main_number'];
				$stickerInfoItemIndividualNumber->sub_number  = $srcStickerParam['individual_sub_number'];
				// データベースに保存する
		    	$stickerInfoItemIndividualNumber->save();
		    	
		    	$dstStickerParam['individual_main_number']  = $stickerInfoItemIndividualNumber->main_number;
		    	$dstStickerParam['individual_sub_number']   = $stickerInfoItemIndividualNumber->sub_number;
			}
			
			// infoItemPos
			$stickerInfoItemPos = $sticker->infoItemPos;
			if ($stickerInfoItemPos) {
				$stickerInfoItemPos->pos_top  = $srcStickerParam['pos_top'];
				$stickerInfoItemPos->pos_left = $srcStickerParam['pos_left'];
				// データベースに保存する
		    	$stickerInfoItemPos->save();
		    	
		    	$dstStickerParam['pos_top']  = $stickerInfoItemPos->pos_top;
		    	$dstStickerParam['pos_left'] = $stickerInfoItemPos->pos_left;
			}
			
			// infoItemDepth
			$stickerInfoItemDepth = $sticker->infoItemDepth;
			if ($stickerInfoItemDepth) {
				$stickerInfoItemDepth->depth = $srcStickerParam['depth'];
				// データベースに保存する
	    		$stickerInfoItemDepth->save();
	    		
	    		$dstStickerParam['depth']  = $stickerInfoItemDepth->depth;
			}
			
			// infoItemColor
			$stickerInfoItemColor = $sticker->infoItemColor;
			if ($stickerInfoItemColor) {
				$stickerInfoItemColor->color = $srcStickerParam['color'];
				// データベースに保存する
		    	$stickerInfoItemColor->save();
		    	
		    	$dstStickerParam['color'] = $stickerInfoItemColor->color;
			}
			
			// contentItem
			$dstContents = array();
			$srcContents = $srcStickerParam['contents'];
			foreach ($srcContents as $srcContent) {
				if ($srcContent['link']['item_type'] == \App\Sticker::$contentItemType['text']) {
					// テキストを作成し、データベースに保存する
					list($contentLink, $contentItem) = $sticker->createContentItemText([
		    			'text' => $srcContent['item']['text'],
					]);
					
					$dstContent = [
						'link' => [
							'id'        => $contentLink->id,
							'item_type' => $contentLink->item_type,
							'item_id'   => $contentLink->item_id,
						],
						'item' => [
							'text' => $contentItem->text,
						],
					];
					array_push($dstContents, $dstContent);
				} else if ($srcContent['link']['item_type'] == \App\Sticker::$contentItemType['image']) {
					if (array_key_exists('item_info', $srcContent)) {  // ファイルがあるなら特別なプロパティitem_infoが用意してある
						// 画像ファイルをアップする
				        list($imageURL, $imagePublicId) = ImageUtil::uploadImage($srcContent['item_info']['selectImageFileInfo']);
				        
				        // ContentItemImageを作成し、データベースに保存する
						list($contentLink, $contentItem) = $sticker->createContentItemImage([
						    'image_url'       => $imageURL,
						    'image_public_id' => $imagePublicId,
						]);
						
						$dstContent = [
							'link' => [
								'id'        => $contentLink->id,
								'item_type' => $contentLink->item_type,
								'item_id'   => $contentLink->item_id,
							],
							'item' => [
								'image_url'       => $contentItem->image_url,
								'image_public_id' => $contentItem->image_public_id,
							],
						];
						array_push($dstContents, $dstContent);
					}
				} else if ($srcContent['link']['item_type'] == \App\Sticker::$contentItemType['video']) {
					if (array_key_exists('item_info', $srcContent)) {  // ファイルがあるなら特別なプロパティitem_infoが用意してある
						// 動画ファイルをアップする
				        list($videoURL, $videoPublicId) = ImageUtil::uploadVideo($srcContent['item_info']['selectVideoFileInfo']);
				
				        // ContentItemVideoを作成し、データベースに保存する
						list($contentLink, $contentItem) = $sticker->createContentItemVideo([
						    'video_url'       => $videoURL,
						    'video_public_id' => $videoPublicId,
						]);
						
						$dstContent = [
							'link' => [
								'id'        => $contentLink->id,
								'item_type' => $contentLink->item_type,
								'item_id'   => $contentLink->item_id,
							],
							'item' => [
								'video_url'       => $contentItem->video_url,
								'video_public_id' => $contentItem->video_public_id,
							],
						];
						array_push($dstContents, $dstContent);
					}
				} else if ($srcContent['link']['item_type'] == \App\Sticker::$contentItemType['taskStartTime']
					|| $srcContent['link']['item_type'] == \App\Sticker::$contentItemType['taskEndTime']) {
					$taskTimeType = null;
					if ($srcContent['link']['item_type'] == \App\Sticker::$contentItemType['taskStartTime']) {
						$taskTimeType = 'taskStartTime';
					} else if ($srcContent['link']['item_type'] == \App\Sticker::$contentItemType['taskEndTime']) {
						$taskTimeType = 'taskEndTime';
					}
					
					// 時刻データを作成し、データベースに保存する
					list($contentLink, $contentItem) = $sticker->createContentItemTaskTime([
			            'task_time_type' => $taskTimeType,
			            'time_zone_type' => $srcContent['item']['time_zone_type'],
			            'year_value'     => $srcContent['item']['year_value'],
			            'month_value'    => $srcContent['item']['month_value'],
			            'day_value'      => $srcContent['item']['day_value'],
			            'hour_value'     => $srcContent['item']['hour_value'],
			            'minute_value'   => $srcContent['item']['minute_value'],
					]);
					
					$dstContent = [
						'link' => [
							'id'        => $contentLink->id,
							'item_type' => $contentLink->item_type,
							'item_id'   => $contentLink->item_id,
						],
						'item' => [
							'time_zone_type' => $contentItem->time_zone_type,
							'year_value'     => $contentItem->year_value,
							'month_value'    => $contentItem->month_value,
							'day_value'      => $contentItem->day_value,
							'hour_value'     => $contentItem->hour_value,
							'minute_value'   => $contentItem->minute_value,
						],
					];
					array_push($dstContents, $dstContent);
				}
			}
			
			$dstStickerParam['contents'] = $dstContents;
			
			array_push($dstStickerParams, $dstStickerParam);
		}
	}
	
	// イベント
	$eventParam = [
		'stickerParams' => $dstStickerParams,
	    'user_id'       => $request->user_id,
	];	

	event((new \App\Events\StickyNoteImport($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
});

Route::post('/work-sticky-note-import-start', function(Request $request) {
	// 既存のふせんを全て削除し、データベースに保存する
	$stickers = \App\Sticker::all();
	foreach ($stickers as $sticker) {
		$sticker->destroySticker();
	}
	
	// ふせんを作る
	$stickerIds = array();  // $stickerIds[stickerIndex] = stickerIndex番目のstickerのid
	
	$srcStickerParams = $request->reqParam['stickerParams'];
	foreach ($srcStickerParams as $srcStickerParam) {
		// ふせんを作成し、データベースに保存する
		$sticker = \App\Sticker::createSticker();
		
		// ふせんにinfoItemを設定する
		if ($sticker) {
			// infoItemIndividualNumber
			$stickerInfoItemIndividualNumber = $sticker->infoItemIndividualNumber;
			if ($stickerInfoItemIndividualNumber) {
				$stickerInfoItemIndividualNumber->main_number = $srcStickerParam['individual_main_number'];
				$stickerInfoItemIndividualNumber->sub_number  = $srcStickerParam['individual_sub_number'];
				// データベースに保存する
		    	$stickerInfoItemIndividualNumber->save();
			}
			
			// infoItemPos
			$stickerInfoItemPos = $sticker->infoItemPos;
			if ($stickerInfoItemPos) {
				$stickerInfoItemPos->pos_top  = $srcStickerParam['pos_top'];
				$stickerInfoItemPos->pos_left = $srcStickerParam['pos_left'];
				// データベースに保存する
		    	$stickerInfoItemPos->save();
			}
			
			// infoItemDepth
			$stickerInfoItemDepth = $sticker->infoItemDepth;
			if ($stickerInfoItemDepth) {
				$stickerInfoItemDepth->depth = $srcStickerParam['depth'];
				// データベースに保存する
	    		$stickerInfoItemDepth->save();
			}
			
			// infoItemColor
			$stickerInfoItemColor = $sticker->infoItemColor;
			if ($stickerInfoItemColor) {
				$stickerInfoItemColor->color = $srcStickerParam['color'];
				// データベースに保存する
		    	$stickerInfoItemColor->save();
			}
			
			array_push($stickerIds, $sticker->id);
		} else {
			array_push($stickerIds, -1);  // idがないときは-1
		}
	}
	
	return $stickerIds;
});

Route::post('/work-sticky-note-import-content-item', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	// ふせんにcontentItemを設定する
	if ($sticker) {
		$item_type = $request->reqParam['item_type'];
		
		if ($item_type == \App\Sticker::$contentItemType['text']) {
			$text = $request->reqParam['text'];
			
			// テキストを作成し、データベースに保存する
			list($contentLink, $contentItem) = $sticker->createContentItemText([
			    'text' => $text,
			]);
		} else if ($item_type == \App\Sticker::$contentItemType['image']) {
			// 画像ファイルをアップする
	        list($imageURL, $imagePublicId) = ImageUtil::uploadImage($request->reqParam['selectImageFileInfo']);
	        
	        // ContentItemImageを作成し、データベースに保存する
			list($contentLink, $contentItem) = $sticker->createContentItemImage([
			    'image_url'       => $imageURL,
			    'image_public_id' => $imagePublicId,
			]);			
		} else if ($item_type == \App\Sticker::$contentItemType['video']) {
			// 動画ファイルをアップする
	        list($videoURL, $videoPublicId) = ImageUtil::uploadVideo($request->reqParam['selectVideoFileInfo']);
	
	        // ContentItemVideoを作成し、データベースに保存する
			list($contentLink, $contentItem) = $sticker->createContentItemVideo([
			    'video_url'       => $videoURL,
			    'video_public_id' => $videoPublicId,
			]);
		} else if ($item_type == \App\Sticker::$contentItemType['taskStartTime']
			|| $item_type == \App\Sticker::$contentItemType['taskEndTime']) {
			$taskTimeType = null;
			if ($item_type == \App\Sticker::$contentItemType['taskStartTime']) {
				$taskTimeType = 'taskStartTime';
			} else if ($item_type == \App\Sticker::$contentItemType['taskEndTime']) {
				$taskTimeType = 'taskEndTime';
			}
			
			// 時刻データを作成し、データベースに保存する
			list($contentLink, $contentItem) = $sticker->createContentItemTaskTime([
			    'task_time_type' => $taskTimeType,
			    'time_zone_type' => $request->time_zone_type,
			    'year_value'     => $request->year_value,
			    'month_value'    => $request->month_value,
			    'day_value'      => $request->day_value,
			    'hour_value'     => $request->hour_value,
			    'minute_value'   => $request->minute_value,
			]);
		}
	}
});

Route::post('/work-sticky-note-import-end', function(Request $request) {
	$stickerParams = \App\Sticker::getStickerParams();
	
	// イベント
	$eventParam = [
		'stickerParams' => $stickerParams,
	    'user_id'       => $request->user_id,
	];	

	event((new \App\Events\StickyNoteImport($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
});

Route::post('/work-sticker-create', function(Request $request) {
	// ふせんを作成する前に、既存のふせんのdepthをきれいにしておく
	// （ふせんを削除したときにdepthが連続値でなくなっているので）
	
	$depthMin = \App\Sticker::$infoItemDepthMin;

	$dstStickerDepths = array();

	$stickerCount = 0;
	
	{
		// 複数個所から同時更新されるのを防ぐためロックしておく
		$stickerInfoItemDepths = \App\StickerInfoItemDepth::orderBy('depth', 'asc')->lockForUpdate()->get();
		foreach ($stickerInfoItemDepths as $index => $stickerInfoItemDepth) {
			$stickerInfoItemDepth->depth = $depthMin + $stickerCount;
			++$stickerCount;

			// データベースに保存する
	    	$stickerInfoItemDepth->save();
	    	
	    	$dstStickerDepth = [
	    		'id'    => $stickerInfoItemDepth->sticker_id,
	    		'depth' => $stickerInfoItemDepth->depth,
	    	];
	    	array_push($dstStickerDepths, $dstStickerDepth);
		}
	}
	
	
	// ふせんを作成し、データベースに保存する
	$sticker = \App\Sticker::createSticker();
	
	$stickerInfoItemIndividualNumber = $sticker->infoItemIndividualNumber;
	$stickerInfoItemPos   = $sticker->infoItemPos;
	$stickerInfoItemDepth = $sticker->infoItemDepth;
	$stickerInfoItemColor = $sticker->infoItemColor;
	
	// イベント
	$eventParam = [
		'id'       => $sticker->id,
		'individual_main_number' => $stickerInfoItemIndividualNumber->main_number,
		'individual_sub_number'  => $stickerInfoItemIndividualNumber->sub_number,
	    'pos_top'  => $stickerInfoItemPos->pos_top,
	    'pos_left' => $stickerInfoItemPos->pos_left,
	    'depth'    => $stickerInfoItemDepth->depth,
	    'color'    => $stickerInfoItemColor->color,
	    'sticker_depths' => $dstStickerDepths,
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
		
		// 動画ファイルの情報を取得し、削除する
		$stickerContentItemVideos = $sticker->contentItemVideos;
		if ($stickerContentItemVideos) {
			foreach ($stickerContentItemVideos as $contentItemVideo) {
				$videoPublicId = $contentItemVideo->video_public_id;
		
				// 動画ファイルを削除する
        		ImageUtil::destroyVideo($videoPublicId);
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
	$stickerParams = \App\Sticker::getStickerParams();
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

Route::put('/work-sticker-info-item-depth-update', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$stickerInfoItemDepth = $sticker->infoItemDepth;
		if ($stickerInfoItemDepth) {
			$changeType = $request->reqParam['change_type'];

			if ($changeType == 'increment') {  // インクリメント
				// 値を1増やして、データベースに保存する
				$stickerInfoItemDepth->increment('depth');
			} else if ($changeType == 'decrement') {  // デクリメント
				// 値を1減らして、データベースに保存する
				$stickerInfoItemDepth->decrement('depth');
			} else if ($changeType == 'set') {  // 値を設定
				$changeValue = $request->reqParam['change_value'];
				// 値を設定する
				$stickerInfoItemDepth->depth = $changeValue;
				// データベースに保存する
	    		$stickerInfoItemDepth->save();
			}
			
			// 値を範囲内におさめる
			$depthMin = \App\Sticker::$infoItemDepthMin;
			$depthMax = \App\Sticker::$infoItemDepthMax;
			
			$isModified = false;
			$modifiedDepth = $stickerInfoItemDepth->depth;
			if ($modifiedDepth < $depthMin) {
				$modifiedDepth = $depthMin;
				$isModified = true;
			} else if ($modifiedDepth > $depthMax) {
				$modifiedDepth = $depthMax;
				$isModified = true;
			}
			
			if ($isModified) {
				// 値を設定する
				$stickerInfoItemDepth->depth = $modifiedDepth;
				// データベースに保存する
	    		$stickerInfoItemDepth->save();
			}
			// TODO(kawadakoujisun): $isModifiedがtrueになるようなときはデータベースに2回書き込んでいることになる。
			//     「現在の値をチェックして値を変更し保存する」という一連の流れにおいて割り込ませないようにして、
			//     データベースへの書き込みは1回で済ますようにしたほうがいいか？
	    	
	    	// イベント
	    	$eventParam = [
	    		'id'      => $sticker->id,
	    		'depth'   => $stickerInfoItemDepth->depth,
	    		'user_id' => $request->user_id,
	    	];
	    	
	    	event((new \App\Events\StickerInfoItemDepthUpdate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
		}
	}
});

Route::put('/work-all-sticker-info-item-depth-update', function(Request $request) {
	$depthMin = \App\Sticker::$infoItemDepthMin;

	$dstStickerDepths = array();

	$stickerCount = 0;
	
	$stickerId = $request->reqParam['id'];
	$changeType = $request->reqParam['change_type'];
	
	if ($changeType == 'frontMost') {  // 最前面へ
		// 複数個所から同時更新されるのを防ぐためロックしておく
		$stickerInfoItemDepths = \App\StickerInfoItemDepth::orderBy('depth', 'asc')->lockForUpdate()->get();
		$stickerNum = \App\StickerInfoItemDepth::count();
		foreach ($stickerInfoItemDepths as $index => $stickerInfoItemDepth) {
			if ($stickerInfoItemDepth->sticker_id == $stickerId) {
				$stickerInfoItemDepth->depth = $depthMin + $stickerNum - 1;
			} else {
				$stickerInfoItemDepth->depth = $depthMin + $stickerCount;
				++$stickerCount;
			}
			// データベースに保存する
	    	$stickerInfoItemDepth->save();
	    	
	    	$dstStickerDepth = [
	    		'id'    => $stickerInfoItemDepth->sticker_id,
	    		'depth' => $stickerInfoItemDepth->depth,
	    	];
	    	array_push($dstStickerDepths, $dstStickerDepth);
		}
	} else if ($changeType == 'backMost') {  // 最背面へ
		// 複数個所から同時更新されるのを防ぐためロックしておく
		$stickerInfoItemDepths = \App\StickerInfoItemDepth::orderBy('depth', 'desc')->lockForUpdate()->get();
		$stickerNum = \App\StickerInfoItemDepth::count();
		foreach ($stickerInfoItemDepths as $index => $stickerInfoItemDepth) {
			if ($stickerInfoItemDepth->sticker_id == $stickerId) {
				$stickerInfoItemDepth->depth = $depthMin;
			} else {
				$stickerInfoItemDepth->depth = $depthMin + $stickerNum - 1 - $stickerCount;
				++$stickerCount;
			}
			// データベースに保存する
	    	$stickerInfoItemDepth->save();
	    	
	    	$dstStickerDepth = [
	    		'id'    => $stickerInfoItemDepth->sticker_id,
	    		'depth' => $stickerInfoItemDepth->depth,
	    	];
	    	array_push($dstStickerDepths, $dstStickerDepth);
		}
	}
	
	if (count($dstStickerDepths) > 0) {
		// イベント
	    $eventParam = [
	    	'sticker_depths' => $dstStickerDepths,
	    	'user_id'        => $request->user_id,
	    ];
	    
	    event((new \App\Events\AllStickerInfoItemDepthUpdate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
	}
});

Route::put('/work-all-sticker-info-item-individual-number-update', function(Request $request) {
	$dstStickerIndividualNumbers = array();
	
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$stickerInfoItemIndividualNumberId = $sticker->infoItemIndividualNumber->id;
		
		$newMainNumber = $request->reqParam['mainNumber'];
		
		// TODO(kawadakoujisun): ここではロックしては外し・・・を3回行っているが、
		//     1回だけ全体をロックし全処理済ませてロックを外すというふうにはできないだろうか？
		
		// main_number更新
		$oldMainNumber = null;
		{
			// 複数個所から同時更新されるのを防ぐためロックしておく
			$stickerInfoItemIndividualNumber = \App\StickerInfoItemIndividualNumber::where('id', $stickerInfoItemIndividualNumberId)->lockForUpdate()->first();
			$oldMainNumber = $stickerInfoItemIndividualNumber->main_number;
			$stickerInfoItemIndividualNumber->main_number = $newMainNumber;
			// データベースに保存する
	    	$stickerInfoItemIndividualNumber->save();
	    	
	    	// $dstStickerIndividualNumbersへの追加は、後で
	    	// \App\StickerInfoItemIndividualNumber::where('main_number', $newMainNumber)
	    	// を処理するので、そこで行われる。
		}

		// 古いmain_numberと同じ値を持つものについて、sub_numberを更新する
		{
			// 複数個所から同時更新されるのを防ぐためロックしておく
			$stickerInfoItemIndividualNumbers
				= \App\StickerInfoItemIndividualNumber::where('main_number', $oldMainNumber)
				->orderBy('sticker_id', 'asc')
				->lockForUpdate()
				->get();
			$stickerInfoItemIndividualNumberCount = $stickerInfoItemIndividualNumbers->count();
			
			if ($stickerInfoItemIndividualNumberCount >= 2) {
				foreach ($stickerInfoItemIndividualNumbers as $index => $stickerInfoItemIndividualNumber) {
					$stickerInfoItemIndividualNumber->sub_number = $index + 1;
					// データベースに保存する
		    		$stickerInfoItemIndividualNumber->save();
		    		
			    	$dstStickerIndividualNumber = [
			    		'id'                     => $stickerInfoItemIndividualNumber->sticker_id,
			    		'individual_main_number' => $stickerInfoItemIndividualNumber->main_number,
			    		'individual_sub_number'  => $stickerInfoItemIndividualNumber->sub_number,
			    	];
			    	array_push($dstStickerIndividualNumbers, $dstStickerIndividualNumber);
				}
			} else if ($stickerInfoItemIndividualNumberCount >= 1) {
				$stickerInfoItemIndividualNumber = $stickerInfoItemIndividualNumbers->first();
				$stickerInfoItemIndividualNumber->sub_number = 0;
				// データベースに保存する
		    	$stickerInfoItemIndividualNumber->save();
		    	
		    	$dstStickerIndividualNumber = [
		    		'id'                     => $stickerInfoItemIndividualNumber->sticker_id,
		    		'individual_main_number' => $stickerInfoItemIndividualNumber->main_number,
		    		'individual_sub_number'  => $stickerInfoItemIndividualNumber->sub_number,
		    	];
		    	array_push($dstStickerIndividualNumbers, $dstStickerIndividualNumber);		    	
			}
		}
	
		// 新しいmain_numberと同じ値を持つものについて、sub_numberを更新する
		{
			// 複数個所から同時更新されるのを防ぐためロックしておく
			$stickerInfoItemIndividualNumbers
				= \App\StickerInfoItemIndividualNumber::where('main_number', $newMainNumber)
				->orderBy('sticker_id', 'asc')
				->lockForUpdate()
				->get();
			$stickerInfoItemIndividualNumberCount = $stickerInfoItemIndividualNumbers->count();
			
			if ($stickerInfoItemIndividualNumberCount >= 2) {
				foreach ($stickerInfoItemIndividualNumbers as $index => $stickerInfoItemIndividualNumber) {
					$stickerInfoItemIndividualNumber->sub_number = $index + 1;
					// データベースに保存する
		    		$stickerInfoItemIndividualNumber->save();
		    		
			    	$dstStickerIndividualNumber = [
			    		'id'                     => $stickerInfoItemIndividualNumber->sticker_id,
			    		'individual_main_number' => $stickerInfoItemIndividualNumber->main_number,
			    		'individual_sub_number'  => $stickerInfoItemIndividualNumber->sub_number,
			    	];
			    	array_push($dstStickerIndividualNumbers, $dstStickerIndividualNumber);
				}
			} else if ($stickerInfoItemIndividualNumberCount >= 1) {
				$stickerInfoItemIndividualNumber = $stickerInfoItemIndividualNumbers->first();
				$stickerInfoItemIndividualNumber->sub_number = 0;
				// データベースに保存する
		    	$stickerInfoItemIndividualNumber->save();
		    	
			    $dstStickerIndividualNumber = [
			    	'id'                     => $stickerInfoItemIndividualNumber->sticker_id,
			    	'individual_main_number' => $stickerInfoItemIndividualNumber->main_number,
			    	'individual_sub_number'  => $stickerInfoItemIndividualNumber->sub_number,
			    ];
			    array_push($dstStickerIndividualNumbers, $dstStickerIndividualNumber);
			}
		}
	}
	
	if (count($dstStickerIndividualNumbers) > 0) {
		// イベント
	    $eventParam = [
	    	'sticker_individual_numbers' => $dstStickerIndividualNumbers,
	    	'user_id'                    => $request->user_id,
	    ];
	    
	    event((new \App\Events\AllStickerInfoItemIndividualNumberUpdate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
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
		
		event((new \App\Events\StickerContentItemImageCreate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
	}
});

Route::delete('/work-sticker-content-item-image-destroy', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$content_link_id = $request->reqParam['content_link_id'];
		
		// 画像ファイルの情報を取得する
		$stickerContentLinks      = $sticker->contentLinks;
		$stickerContentItemImages = $sticker->contentItemImages;
		
		if ($stickerContentLinks && $stickerContentItemImages) {
			$contentLink      = $stickerContentLinks->where('id', $content_link_id)->first();
			if ($contentLink) {
				$contentItemImage = $stickerContentItemImages->where('id', $contentLink->item_id)->first();
				
				if ($contentItemImage) {
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
				    
				    event((new \App\Events\StickerContentItemImageDestroy($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
				}
			}
		}
	}
});

Route::post('/work-sticker-content-item-video-create', function(Request $request) {
	// Log::debug('my_debug_log: '. 'Route::post /work-sticker-content-item-video-create');
	// logger($request);
	
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		// 動画ファイルをアップする
        list($videoURL, $videoPublicId) = ImageUtil::uploadVideo($request->reqParam['selectVideoFileInfo']);

        // ContentItemVideoを作成し、データベースに保存する
		list($contentLink, $contentItem) = $sticker->createContentItemVideo([
		    'video_url'       => $videoURL,
		    'video_public_id' => $videoPublicId,
		]);
		
		// イベント
	    $eventParam = [
	    	'id'                => $sticker->id,
	    	'content_link_id'   => $contentLink->id,
	    	'content_item_type' => $contentLink->item_type,
	    	'content_item_id'   => $contentLink->item_id,
		    'video_url'         => $videoURL,
		    'video_public_id'   => $videoPublicId,
	    	'user_id'           => $request->user_id,
	    ];
		
		event((new \App\Events\StickerContentItemVideoCreate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
	}
});

Route::delete('/work-sticker-content-item-video-destroy', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$content_link_id = $request->reqParam['content_link_id'];
		
		// 動画ファイルの情報を取得する
		$stickerContentLinks      = $sticker->contentLinks;
		$stickerContentItemVideos = $sticker->contentItemVideos;
		
		if ($stickerContentLinks && $stickerContentItemVideos) {
			$contentLink      = $stickerContentLinks->where('id', $content_link_id)->first();
			if ($contentLink) {
				$contentItemVideo = $stickerContentItemVideos->where('id', $contentLink->item_id)->first();
				
				if ($contentItemVideo) {
					$videoPublicId = $contentItemVideo->video_public_id;
					
					// 動画ファイルを削除する
			        ImageUtil::destroyVideo($videoPublicId);
					
					// ContentItemVideoを削除し、データベースに保存する
					$sticker->destroyContentItem([
					    'content_link_id' => $content_link_id,
					]);
				    
				    // イベント
				    $eventParam = [
				    	'id'              => $sticker->id,
				    	'content_link_id' => $content_link_id,
				    	'user_id'         => $request->user_id,
				    ];
				    
				    event((new \App\Events\StickerContentItemVideoDestroy($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
				}
			}
		}
	}
});

Route::post('/work-sticker-content-item-task-time-create', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$taskTimeType = $request->reqParam['taskTimeType'];  // 'taskStartTime' or 'taskEndTime'
		$timeZoneType = $request->reqParam['timeZoneType'];
		$yearValue    = $request->reqParam['yearValue'];
		$monthValue   = $request->reqParam['monthValue'];
		$dayValue     = $request->reqParam['dayValue'];
		$hourValue    = $request->reqParam['hourValue'];
		$minuteValue  = $request->reqParam['minuteValue'];
		
		// 時刻データを作成し、データベースに保存する
		list($contentLink, $contentItem) = $sticker->createContentItemTaskTime([
            'task_time_type' => $taskTimeType,
            'time_zone_type' => $timeZoneType,
            'year_value'     => $yearValue,
            'month_value'    => $monthValue,
            'day_value'      => $dayValue,
            'hour_value'     => $hourValue,
            'minute_value'   => $minuteValue,
		]);
		
		if ($contentLink != null && $contentItem != null) {
		    // イベント
		    $eventParam = [
		    	'id'                => $sticker->id,
		    	'content_link_id'   => $contentLink->id,
		    	'content_item_type' => $contentLink->item_type,
		    	'content_item_id'   => $contentLink->item_id,
	            'time_zone_type'    => $timeZoneType,
	            'year_value'        => $yearValue,
	            'month_value'       => $monthValue,
	            'day_value'         => $dayValue,
	            'hour_value'        => $hourValue,
	            'minute_value'      => $minuteValue,
		    	'user_id'           => $request->user_id,
		    ];
		    
		    event((new \App\Events\StickerContentItemTaskTimeCreate($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
		}
	}
});

Route::delete('/work-sticker-content-item-task-time-destroy', function(Request $request) {
	$sticker = \App\Sticker::find($request->reqParam['id']);

	if ($sticker) {
		$content_link_id = $request->reqParam['content_link_id'];
		
		// 時刻データを削除し、データベースに保存する
		$sticker->destroyContentItem([
		    'content_link_id' => $content_link_id,
		]);
	    
	    // イベント
	    $eventParam = [
	    	'id'              => $sticker->id,
	    	'content_link_id' => $content_link_id,
	    	'user_id'         => $request->user_id,
	    ];
	    
	    event((new \App\Events\StickerContentItemTaskTimeDestroy($eventParam)));  // 自分にも送信したいのでdontBroadcastToCurrentUserは付けない
	}
});
