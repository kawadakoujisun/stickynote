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
    ];
    // ↑ $contentItemTypeで定義しているのと同じ値を使っている箇所
    // resources/js/components/WorkMenuBarComponent.vue
    // resources/js/components/WorkMountComponent.vue
    // resources/js/components/WorkStickerEditWindowComponent.vue
    // ↑ これらの箇所には「app/Sticker.phpで値を定義している」というコメントをいれてある。
    
    public static $infoItemDepthDefault = 400;
    public static $infoItemDepthMin     = 200;
    public static $infoItemDepthMax     = 600;
    
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
     * ※StickerInfoItemDepth生成するときはcreateStickerを使用して下さい。
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
     * Stickerを新規作成する。
     * Stickerが所有するStickerInfo〇〇（
     * StickerInfoItemPos、
     * StickerInfoItemDepth、
     * StickerInfoItemColor
     * ）も生成する。
     */
    public static function createSticker()
    {
        $stickerNumBeforeCreate = self::count();
        
        $sticker = self::create();
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
            }
        }
    }
}