<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColorRect extends Model
{
    protected $fillable = [
        'pos_top',
        'pos_left',
        'color',
    ];
}
