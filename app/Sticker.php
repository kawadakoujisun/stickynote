<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    public static $contentItemType = [
        'text'  => 1,
        'image' => 2,
        'video' => 3,
    ];
    
    /**
     * このStickerが所有するStickerInfoItemPos。（StickerInfoItemPosモデルとの関係を定義）
     */
    public function infoItemPos()
    {
        return $this->hasOne(StickerInfoItemPos::class);
    }
    
    /**
     * このStickerが所有するStickerInfoItemColor。（StickerInfoItemColorモデルとの関係を定義）
     */
    public function infoItemColor()
    {
        return $this->hasOne(StickerInfoItemColor::class);
    }
    
    /**
     * このStickerが所有するStickerContentLink。（StickerContentLinkモデルとの関係を定義）
     * StickerContentLinkを生成するときはcreateContentItem〇〇を使用して下さい。
     */
    public function contentLinks()
    {
        return $this->hasMany(StickerContentLink::class);
    }
    
    /**
     * このStickerが所有するStickerContentItemText。（StickerContentItemTextモデルとの関係を定義）
     * StickerContentItemTextを生成するするときはcreateContentItemTextを使用して下さい。
     */
    public function contentItemTexts()
    {
        return $this->hasMany(StickerContentItemText::class);
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
        return $contentItem;
    }
}