<?php

namespace App\ProjectWork;

use Cloudinary\Cloudinary;  // vendorにあるcloudinaryを追加

class ImageUtil
{
    private static function setupCloudinaryUploadAPI()
    {
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ],
        ]);
        
        $uploadApi = $cloudinary->uploadApi();
        
        return $uploadApi;
    }

    //
    // image
    //

    public static function uploadImage($uploadImageFile)
    {
        $uploadApi = self::setupCloudinaryUploadAPI();
        
        // 画像ファイルをCloudinaryにアップする
        $apiResponse = $uploadApi->upload(
            $uploadImageFile,
            [
                "resource_type" => "image",
                "folder"        => "stickynote_uploads/images"
            ]
        );
        
        $imageURL      = $apiResponse["secure_url"];
        $imagePublicId = $apiResponse["public_id"];
        
        return [$imageURL, $imagePublicId];
    }
    
    public static function destroyImage($destroyImagePublicId)
    {
        $uploadApi = self::setupCloudinaryUploadAPI();
        
        // 画像ファイルをCloudinaryから削除する
        $uploadApi->destroy(
            $destroyImagePublicId,
            [
                "resource_type" => "image",
            ]
        );
    }
    
    //
    // video
    //

    public static function uploadVideo($uploadVideoFile)
    {
        $uploadApi = self::setupCloudinaryUploadAPI();
        
        // 動画ファイルをCloudinaryにアップする
        $apiResponse = $uploadApi->upload(
            $uploadVideoFile,
            [
                "resource_type" => "video",
                "folder"        => "stickynote_uploads/videos"
            ]
        );
        
        $videoURL      = $apiResponse["secure_url"];
        $videoPublicId = $apiResponse["public_id"];

        return [$videoURL, $videoPublicId];
    }
    
    public static function destroyVideo($destroyVideoPublicId)
    {
        $uploadApi = self::setupCloudinaryUploadAPI();
        
        // 動画ファイルをCloudinaryから削除する
        $uploadApi->destroy(
            $destroyVideoPublicId,
            [
                "resource_type" => "video",
            ]    
        );
    }    
}