<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerInfoItemDepth extends Model
{
    protected $fillable = [
        'depth',
    ];

    /**
     * このStickerInfoItemColorを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }
}
