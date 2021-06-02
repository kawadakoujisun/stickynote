<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
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
     * このStickerが所有するStickerContent。（StickerContentモデルとの関係を定義）
     */
    public function content()
    {
        return $this->hasOne(StickerContent::class);
    }
}