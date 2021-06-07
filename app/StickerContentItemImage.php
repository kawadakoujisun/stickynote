<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerContentItemImage extends Model
{
    protected $fillable = [
        'image_url',
        'image_public_id',
    ];

    /**
     * このStickerContentItemImageを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }
}
