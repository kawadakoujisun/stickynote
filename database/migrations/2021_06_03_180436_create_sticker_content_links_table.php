<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStickerContentLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sticker_content_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sticker_id');
            $table->unsignedInteger('item_type');
            $table->unsignedBigInteger('item_id');
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
        Schema::dropIfExists('sticker_content_links');
    }
}
