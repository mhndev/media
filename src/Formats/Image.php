<?php
/**
 * Created by PhpStorm.
 * User: Majid
 * Date: 8/4/2016
 * Time: 3:19 PM
 */
namespace mhndev\media\Formats;

use mhndev\media\File;
use Intervention\Image\ImageManager;

/**
 * Class Image
 * @package mhndev\media\Formats
 */
class Image extends File
{

    /**
     * @var ImageManager
     */
    protected static $manipulator;


    /**
     * @return array
     */
    public static function getMimeTypes()
    {
        return [
            'jpg|jpeg|jpe'                 => 'image/jpeg',
            'gif'                          => 'image/gif',
            'png'                          => 'image/png',
            'bmp'                          => 'image/bmp',
            'tif|tiff'                     => 'image/tiff',
            'ico'                          => 'image/x-icon',
        ];
    }


    /**
     * @param $imagePath
     * @param $pathToStoreCache
     * @param $imageType
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function createCache($imagePath, $pathToStoreCache, $imageType, $width = 200, $height = 200)
    {
        self::$manipulator = new ImageManager();

        $filename = self::getFileNameWithoutExtension($imagePath);
        $extension = self::getFileExtension($imagePath);
        $cacheName = $filename . "-" . $width . "-" . $height . "." . $extension;

        if (!file_exists($pathToStoreCache . $imageType . "/" . $cacheName)) {
            $result =  self::$manipulator->make($imagePath)->resize($width, $height)->save(self::getCacheStoragePath('image', $imageType) . "/" . $imageType ."/".$cacheName)->basePath();
        }else{
            $result = self::getCacheStoragePath('image', $imageType) . "/" . $cacheName;
        }



        return 'files/image/'.$imageType.'/'.basename($result);
    }




}
