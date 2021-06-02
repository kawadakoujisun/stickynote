<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerContentItemText extends Model
{
    protected $fillable = [
        'text',
    ];

    /**
     * このStickerContentItemTextを所有するStickerContent。（StickerContentモデルとの関係を定義）
     */
    public function content()
    {
        return $this->belongsTo(StickerContent::class, 'content_id');  // 外部キーがデフォルトの名前ではないので指定しておく
    }
}
