<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerInfoItemColor extends Model
{
    protected $fillable = [
        'color',
    ];

    /**
     * このStickerInfoItemColorを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function stikcer()
    {
        return $this->belongsTo(Sticker::class);
    }
}
