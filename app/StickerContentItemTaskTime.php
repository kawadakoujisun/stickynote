<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StickerContentItemTaskTime extends Model
{
    protected $fillable = [
        'time_zone_type',
        'year_value',
        'month_value',
        'day_value',
        'hour_value',
        'minute_value',
    ];
    
    /**
     * このStickerContentItemTaskTimeを所有するSticker。（Stickerモデルとの関係を定義）
     */
    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }
}
