<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerInfoItemPos extends Model
{
    protected $table = 'sticker_info_item_poss';  // テーブルがデフォルトの名前ではないので指定しておく
    
    protected $fillable = [
        'pos_top',
        'pos_left'
    ];

    /**
     * このStickerInfoItemPosを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function stikcer()
    {
        return $this->belongsTo(Sticker::class);
    }
}
