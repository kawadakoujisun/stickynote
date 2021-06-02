<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStickerInfoItemColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sticker_info_item_colors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sticker_id');
            $table->unsignedInteger('color');
            $table->timestamps();
            
            // 外部キー制約
            $table->foreign('sticker_id')->references('id')->on('stickers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sticker_info_item_colors');
    }
}
