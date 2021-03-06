<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerContentItemText extends Model
{
    protected $fillable = [
        'text',
    ];

    /**
     * このStickerContentItemTextを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }
}
