<?php
/**
 * Created by PhpStorm.
 * User: Majid
 * Date: 8/4/2016
 * Time: 5:13 PM
 */
namespace mhndev\media;

use mhndev\media\Exceptions\DestinationDirectoryNotExist;
use mhndev\media\Exceptions\FileNotExistException;

/**
 * Class File
 * @package mhndev\media
 */
class File
{

    /**
     * @var array
     */
    protected static $config;


    /**
     * @param array $config
     */
    public static function config(array $config)
    {
        self::$config = $config;
    }



    /**
     * @param $path
     * @return string
     * @throws FileNotExistException
     */
    public static function getFileMimeType($path)
    {
        if(!is_readable($path))
            throw new FileNotExistException;

        return mime_content_type($path);
    }


    /**
     * @param $path
     * @return float
     * @throws FileNotExistException
     */
    public static function getFileSize($path)
    {
        if(!is_readable($path))
            throw new FileNotExistException;

        return filesize($path)/1024;
    }

    /**
     * @param $path
     * @param $to
     * @return string
     * @throws DestinationDirectoryNotExist
     * @throws FileNotExistException
     */
    public static function moveFile($path, $to)
    {
        if(!is_readable($path))
            throw new FileNotExistException;

        if(!is_dir($to)){
            throw new DestinationDirectoryNotExist;
        }

        $fileName = self::getFileNameWithoutExtension($path).'.'.self::getFileExtension($path);

        $newFilePath = $to.DIRECTORY_SEPARATOR.$fileName;
        rename($path, $newFilePath);

        return $newFilePath;
    }

    /**
     * @param $path
     * @param $to
     */
    public static function copyFile($path, $to)
    {
        $destination = $to.DIRECTORY_SEPARATOR.pathinfo($path)['filename'];

        copy($path, $destination);
    }

    /**
     * @param $path
     */
    public static function deleteFile($path)
    {
        unlink($path);
    }

    /**
     * @param $path
     * @throws DestinationDirectoryNotExist
     */
    public static function deleteDirectory($path)
    {
        if(!is_dir($path))
            throw new DestinationDirectoryNotExist;

        rmdir($path);
    }


    /**
     * @param $path
     * @return mixed
     */
    public static function getFileExtension($path)
    {
        return pathinfo($path)['extension'];
    }

    /**
     * @param $path
     * @return string
     */
    public static function getFileNameWithoutExtension($path)
    {
        $ext = self::getFileExtension($path);

        return basename($path,$ext);
    }

    /**
     * @param $path
     * @param $newName
     * @return string
     */
    public static function renameFile($path, $newName)
    {
        $dirname = pathinfo($path)['dirname'];

        return self::moveFile($path, $dirname.DIRECTORY_SEPARATOR.$newName);
    }


    /**
     * @param string $mimeType
     * @param string $type
     * @return string
     */
    protected static function getCacheStoragePath($mimeType, $type)
    {
        return self::$config['formats'][$mimeType][$type]['cacheDirectory'];
    }


}
