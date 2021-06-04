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
    // ↑ $contentItemTypeで定義しているのと同じ値を使っている箇所
    // resources/js/components/WorkMountComponent.vue
    // resources/js/components/WorkStickerEditWindowComponent.vue
    // ↑ これらの箇所には「app/Sticker.phpで値を定義している」というコメントをいれてある。
    
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
            }
        }
    }
}