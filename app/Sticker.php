<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\ProjectWork\ImageUtil;  // 追加

class Sticker extends Model
{
    public static $contentItemType = [
        'text'  => 1,
        'image' => 2,
        'video' => 3,
        'taskStartTime' => 4,
        'taskEndTime'   => 5,
    ];
    // ↑ $contentItemTypeで定義しているのと同じ値を使っている箇所
    // resources/js/components/ReadMountComponent.vue
    // resources/js/components/WorkMenuBarComponent.vue
    // resources/js/components/WorkMountComponent.vue
    // resources/js/components/WorkStickerEditWindowComponent.vue
    // resources/js/ProjectWorkCommonScript.js
    // ↑ これらの箇所には「app/Sticker.phpで値を定義している」というコメントをいれてある。
    
    public static $infoItemDepthDefault = 400;
    public static $infoItemDepthMin     = 200;
    public static $infoItemDepthMax     = 600;
    
    public static $infoItemIndividualMainNumberMin = 1;
    public static $infoItemIndividualMainNumberMax = 1000000;
    
    /**
     * このStickerが所有するStickerInfoItemIndividualNumber。（StickerInfoItemIndividualNumberモデルとの関係を定義）
     * ※StickerInfoItemIndividualNumberを生成するときはcreateStickerを使用して下さい。
     */
    public function infoItemIndividualNumber()
    {
        return $this->hasOne(StickerInfoItemIndividualNumber::class);
    }    
    
    /**
     * このStickerが所有するStickerInfoItemPos。（StickerInfoItemPosモデルとの関係を定義）
     * ※StickerInfoItemPosを生成するときはcreateStickerを使用して下さい。
     */
    public function infoItemPos()
    {
        return $this->hasOne(StickerInfoItemPos::class);
    }

    /**
     * このStickerが所有するStickerInfoItemDepth。（StickerInfoItemDepthモデルとの関係を定義）
     * ※StickerInfoItemDepthを生成するときはcreateStickerを使用して下さい。
     */
    public function infoItemDepth()
    {
        return $this->hasOne(StickerInfoItemDepth::class);
    }    
    
    /**
     * このStickerが所有するStickerInfoItemColor。（StickerInfoItemColorモデルとの関係を定義）
     * ※StickerInfoItemColorを生成するときはcreateStickerを使用して下さい。
     */
    public function infoItemColor()
    {
        return $this->hasOne(StickerInfoItemColor::class);
    }
    
    /**
     * このStickerが所有するStickerContentLink。（StickerContentLinkモデルとの関係を定義）
     * ※StickerContentLinkを生成するときはcreateContentItem〇〇を使用して下さい。
     */
    public function contentLinks()
    {
        return $this->hasMany(StickerContentLink::class);
    }
    
    /**
     * このStickerが所有するStickerContentItemText。（StickerContentItemTextモデルとの関係を定義）
     * ※StickerContentItemTextを生成するするときはcreateContentItemTextを使用して下さい。
     */
    public function contentItemTexts()
    {
        return $this->hasMany(StickerContentItemText::class);
    }
    
    /**
     * このStickerが所有するStickerContentItemImage。（StickerContentItemImageモデルとの関係を定義）
     * ※StickerContentItemImageを生成するするときはcreateContentItemImageを使用して下さい。
     */
    public function contentItemImages()
    {
        return $this->hasMany(StickerContentItemImage::class);
    }    
    
    /**
     * このStickerが所有するStickerContentItemVideo。（StickerContentItemVideoモデルとの関係を定義）
     * ※StickerContentItemVideoを生成するするときはcreateContentItemVideoを使用して下さい。
     */
    public function contentItemVideos()
    {
        return $this->hasMany(StickerContentItemVideo::class);
    }
    
    /**
     * このStickerが所有するStickerContentItemTaskTime。（StickerContentItemTaskTimeモデルとの関係を定義）
     * ※StickerContentItemTaskTimeを生成するするときはcreateContentItemTaskTimeを使用して下さい。
     */
    public function contentItemTaskTimes()
    {
        return $this->hasMany(StickerContentItemTaskTime::class);
    }    
    
    /**
     * Stickerを新規作成する。
     * Stickerが所有するStickerInfo〇〇（
     * StickerInfoItemIndividualNumber、
     * StickerInfoItemPos、
     * StickerInfoItemDepth、
     * StickerInfoItemColor
     * ）も生成する。
     */
    public static function createSticker()
    {
        $stickerNumBeforeCreate = self::count();
        
        $individualMainNumberLastBeforeCreate = self::$infoItemIndividualMainNumberMin -1;  // 後で+1するので
        if (\App\StickerInfoItemIndividualNumber::count() > 0) {
            $individualMainNumberLastBeforeCreate = \App\StickerInfoItemIndividualNumber::max('main_number');
                // TODO(kawadakoujisun): 割り込まれて番号被りが起きると困るのでロックすべきか？ depthも被らせたくないし。
        }
        
        $sticker = self::create();
        $sticker->infoItemIndividualNumber()->create([
            'main_number' => $individualMainNumberLastBeforeCreate +1,  // TODO(kawadakoujisun): Maxを越えないかチェック
            'sub_number'  => 0,
        ]);
        $sticker->infoItemPos()->create([
            'pos_top'  => 0,
            'pos_left' => 0,
        ]);
        $sticker->infoItemDepth()->create([
            // 'depth' => self::$infoItemDepthDefault,
            'depth' => self::$infoItemDepthMin + $stickerNumBeforeCreate,  // TODO(kawadakoujisun): Maxを越えないかチェック
        ]);
        $sticker->infoItemColor()->create([
            'color' => 0xffaaaa,
        ]);
        return $sticker;
    }
    
    /*
     * このStickerを削除する。
     * このStickerが所有する画像ファイルや動画ファイルも削除する。
     */
    public function destroySticker()
    {
		// 画像ファイルの情報を取得し、削除する
		$stickerContentItemImages = $this->contentItemImages;
		if ($stickerContentItemImages) {
			foreach ($stickerContentItemImages as $contentItemImage) {
				$imagePublicId = $contentItemImage->image_public_id;
		
				// 画像ファイルを削除する
        		ImageUtil::destroyImage($imagePublicId);
			}
		}
		
		// 動画ファイルの情報を取得し、削除する
		$stickerContentItemVideos = $this->contentItemVideos;
		if ($stickerContentItemVideos) {
			foreach ($stickerContentItemVideos as $contentItemVideo) {
				$videoPublicId = $contentItemVideo->video_public_id;
		
				// 動画ファイルを削除する
        		ImageUtil::destroyVideo($videoPublicId);
			}
		}
		
		// ふせんを削除し、データベースに保存する
		$this->delete();
    }
    
    /**
     * このStickerが所有するStickerContentItemTextを生成する。
     * このStickerが所有するStickerContentLinkを生成し、そこに先ほど生成したもののidを設定する。
     */
    public function createContentItemText($arg)
    {
        $contentItem = $this->contentItemTexts()->create($arg);
        $contentLink = $this->contentLinks()->create([
            'item_type' => self::$contentItemType['text'],
            'item_id'   => $contentItem->id,
        ]);
        return [ $contentLink, $contentItem ];
    }
    
    /**
     * このStickerが所有するStickerContentItemImageを生成する。
     * このStickerが所有するStickerContentLinkを生成し、そこに先ほど生成したもののidを設定する。
     */
    public function createContentItemImage($arg)
    {
        $contentItem = $this->contentItemImages()->create($arg);
        $contentLink = $this->contentLinks()->create([
            'item_type' => self::$contentItemType['image'],
            'item_id'   => $contentItem->id,
        ]);
        return [ $contentLink, $contentItem ];
    }    
    
    /**
     * このStickerが所有するStickerContentItemVideoを生成する。
     * このStickerが所有するStickerContentLinkを生成し、そこに先ほど生成したもののidを設定する。
     */
    public function createContentItemVideo($arg)
    {
        $contentItem = $this->contentItemVideos()->create($arg);
        $contentLink = $this->contentLinks()->create([
            'item_type' => self::$contentItemType['video'],
            'item_id'   => $contentItem->id,
        ]);
        return [ $contentLink, $contentItem ];
    }
    
    /**
     * このStickerが所有するStickerContentItemTaskTimeを生成する。
     * このStickerが所有するStickerContentLinkを生成し、そこに先ほど生成したもののidを設定する。
     */
    public function createContentItemTaskTime($arg)
    {
        $item_type = null;
        if ($arg['task_time_type'] == 'taskStartTime') {
            $item_type = self::$contentItemType['taskStartTime'];
        } else if ($arg['task_time_type'] == 'taskEndTime') {
            $item_type = self::$contentItemType['taskEndTime'];
        }
        
        if ($item_type != null) {
            $contentItem = $this->contentItemTaskTimes()->create($arg);
            $contentLink = $this->contentLinks()->create([
                'item_type' => $item_type,
                'item_id'   => $contentItem->id,
            ]);
            return [ $contentLink, $contentItem ];
        } else {
            return [ null, null ];
        }
    }
    
    /**
     * このStickerが所有するStickerContentItem〇〇とそれと関連があるStickerContentLinkを削除する。
     */
    public function destroyContentItem($arg)
    {
        $contentLink = $this->contentLinks()->where('id', $arg['content_link_id'])->first();
        if ($contentLink) {
            $item_type = $contentLink->item_type;
            $item_id   = $contentLink->item_id;
            
            $contentLink->delete();
            
            if ($item_type == self::$contentItemType['text']) {
                $contentItem = $this->contentItemTexts()->where('id', $item_id)->first();
                if ($contentItem) {
                    $contentItem->delete();
                }
            } else if ($item_type == self::$contentItemType['image']) {
                $contentItem = $this->contentItemImages()->where('id', $item_id)->first();
                if ($contentItem) {
                    $contentItem->delete();
                }
            } else if ($item_type == self::$contentItemType['video']) {
                $contentItem = $this->contentItemVideos()->where('id', $item_id)->first();
                if ($contentItem) {
                    $contentItem->delete();
                }
            } else if ($item_type == self::$contentItemType['taskStartTime']
                || $item_type == self::$contentItemType['taskEndTime']) {
                $contentItem = $this->contentItemTaskTimes()->where('id', $item_id)->first();
                if ($contentItem) {
                    $contentItem->delete();
                }
            }
        }
    }
    
    /**
     * 個別番号のサブ番号を振り直してデータベースに保存する
     * 更新した個別番号に関する情報の配列を返す
     */    
    public static function updateInfoItemIndividualSubNumber($individualMainNumber) {
        $dstStickerIndividualNumbers = array();

		// $individualMainNumberと同じmain_numberを持つものについて、sub_numberを更新する
		{
			// 複数個所から同時更新されるのを防ぐためロックしておく
			$stickerInfoItemIndividualNumbers
				= \App\StickerInfoItemIndividualNumber::where('main_number', $individualMainNumber)
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

        return $dstStickerIndividualNumbers;
    }
    
    /**
     * stickerParamsを取得する
     */
    public static function getStickerParams()
    {
    	$stickerParams = array();
    
        $stickers = self::all();
        foreach ($stickers as $sticker) {
            $stickerInfoItemIndividualNumber = $sticker->infoItemIndividualNumber;
    		$stickerInfoItemPos       = $sticker->infoItemPos;
    		$stickerInfoItemDepth     = $sticker->infoItemDepth;
    		$stickerInfoItemColor     = $sticker->infoItemColor;
    		$stickerContentLinks      = $sticker->contentLinks;
    		$stickerContentItemTexts  = $sticker->contentItemTexts;
    		$stickerContentItemImages = $sticker->contentItemImages;
    		$stickerContentItemVideos = $sticker->contentItemVideos;
    		$stickerContentItemTaskTimes = $sticker->contentItemTaskTimes;
    		
    		if ($stickerInfoItemIndividualNumber && $stickerInfoItemPos && $stickerInfoItemDepth && $stickerInfoItemColor) {
    			$stickerParam = [
    				'id'       => $sticker->id,
    				'individual_main_number' => $stickerInfoItemIndividualNumber->main_number,
    				'individual_sub_number'  => $stickerInfoItemIndividualNumber->sub_number,
    				'pos_top'  => $stickerInfoItemPos->pos_top,
    				'pos_left' => $stickerInfoItemPos->pos_left,
    				'depth'    => $stickerInfoItemDepth->depth,
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
    					
    					if ($item_type == self::$contentItemType['text']) {
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
    					} else if ($item_type == self::$contentItemType['image']) {
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
    					} else if ($item_type == self::$contentItemType['video']) {
    						if ($stickerContentItemVideos) {
    							$contentItemVideo = $stickerContentItemVideos->where('id', $item_id)->first();
    							if ($contentItemVideo) {
    								$videoURL      = $contentItemVideo->video_url;
    								$videoPublicId = $contentItemVideo->video_public_id;
    								
    								$item = [
    								    'video_url'       => $videoURL,
    								    'video_public_id' => $videoPublicId,
    								];
    								
    								$content['item'] = $item;
    								
    							    array_push($contents, $content);
    							}
    						}
    					} else if ($item_type == self::$contentItemType['taskStartTime']
    					    || $item_type == self::$contentItemType['taskEndTime']) {
                            if ($stickerContentItemTaskTimes) {
    							$contentItemTaskTime = $stickerContentItemTaskTimes->where('id', $item_id)->first();
    							if ($contentItemTaskTime) {
    								$item = [
                                        'time_zone_type' => $contentItemTaskTime->time_zone_type,
                                        'year_value'     => $contentItemTaskTime->year_value,
                                        'month_value'    => $contentItemTaskTime->month_value,
                                        'day_value'      => $contentItemTaskTime->day_value,
                                        'hour_value'     => $contentItemTaskTime->hour_value,
                                        'minute_value'   => $contentItemTaskTime->minute_value,
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
    }
   
    /**
     * sortStickerParamsで使用する比較関数
     * $a, $bはstickerParamです。（sortStickerParams関数に渡した$stickerParams配列の要素）
     */ 
    public static function compareByIndividualNumber($a, $b) {
        if ($a['individual_main_number'] == $b['individual_main_number']) {
             return $a['individual_sub_number'] - $b['individual_sub_number'];
        } else {
            return $a['individual_main_number'] - $b['individual_main_number'];
        }
    }   
    
    /**
     * stickerParamsをソートする
     * sortが参照渡し(usortが参照渡し)なので、$stickerParamsは参照渡しにしておく。
     */    
    public static function sortStickerParams(&$stickerParams) {
        usort($stickerParams, array('\App\Sticker', 'compareByIndividualNumber'));
    }
}