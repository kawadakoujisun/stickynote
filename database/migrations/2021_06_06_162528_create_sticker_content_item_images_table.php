<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStickerContentItemImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sticker_content_item_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sticker_id');
            $table->string('image_url', 2048);  // URLは2048とっておいたほうがよいそうなので2047にしなかった。
            $table->string('image_public_id', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sticker_content_item_images');
    }
}
