<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerContent extends Model
{
    /**
     * このStickerContentを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function stikcer()
    {
        return $this->belongsTo(Sticker::class);
    }
    
    /**
     * このStickerContentが所有するStickerContentItemText。（StickerContentItemTextモデルとの関係を定義）
     * このStickerContentが所有するStickerContentItemTextを生成する際はcreateContentItemTextを使用して下さい。
     */
    public function contentItemTexts()
    {
        return $this->hasMany(StickerContentItemText::class, 'content_id');  // 外部キーがデフォルトの名前ではないので指定しておく
    }
    
    /**
     * このStickerContentが所有するStickerContentItemTextを生成し、このStickerContentにそのidを設定する。
     */
    public function createContentItemText($arg)
    {
        $contentItem = $this->contentItemTexts()->create($arg);
        
        $this->item_type = 1;                 // 1=text
        $this->item_id   = $contentItem->id;
        $this->save();
        
        return $contentItem;
    }
}
