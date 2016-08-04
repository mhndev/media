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


    public function setManipulator()
    {
        self::$manipulator = new ImageManager();
    }

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


    protected function createCache($imagePath, $pathToStoreCache, $width = 200, $height = 200, $imageType)
    {
        $filename = $this->getFileNameWithoutExtension($imagePath);
        $extension = $this->getFileExtension($imagePath);
        $cacheName = $filename . "-" . $width . "-" . $height . "." . $extension;

        if (!file_exists($pathToStoreCache . $imageType . "/" . $cacheName)) {

            return self::$manipulator->make($imagePath)->resize($width, $height)->save($this->getCacheStoragePath('image', $imageType) . $imageType . "/" . $cacheName)->basePath();

        }
        return self::getCacheStoragePath('image', $imageType) . $imageType . "/" . $cacheName;


    }


}
