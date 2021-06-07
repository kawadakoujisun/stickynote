<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerContentItemVideo extends Model
{
    protected $fillable = [
        'video_url',
        'video_public_id',
    ];

    /**
     * このStickerContentItemVideoを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }
}
