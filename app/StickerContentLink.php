<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerContentLink extends Model
{
    protected $fillable = [
        'item_type',
        'item_id',
    ];
    
    /**
     * このStickerContentItemTextを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }
}
