<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerInfoItemIndividualNumber extends Model
{
    protected $fillable = [
        'main_number',
        'sub_number',
    ];
    
    /**
     * このStickerInfoItemColorを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }
}
