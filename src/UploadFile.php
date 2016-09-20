<?php
/**
 * Created by PhpStorm.
 * User: Majid
 * Date: 8/4/2016
 * Time: 6:58 PM
 */
namespace mhndev\media;

use mhndev\media\Exceptions\ExceedMaxAllowedFileUpload;
use mhndev\media\Exceptions\InvalidMimeTypeException;
use mhndev\media\Exceptions\NoFileUploadedException;
use mhndev\media\Exceptions\NotEnoughStorageException;
use mhndev\media\Formats\Audio;
use mhndev\media\Formats\Image;
use mhndev\media\Formats\Text;
use mhndev\media\Formats\Video;

/**
 * Class UploadFile
 * @package mhndev\media
 */
class UploadFile extends File
{


    /**
     * @param string $key
     * @param string $type
     * @return array|string
     * @throws NoFileUploadedException
     */
    public static function store($key, $type)
    {
        $result = [];

        if(empty($_FILES) || empty($_FILES[$key])){
            throw new NoFileUploadedException;
        }


        //multiple uploaded files and array
        if(is_array($_FILES[$key]['name']) && count($_FILES[$key]['name']) > 1 ){
            $files = self::diverse_array($_FILES[$key]);

            foreach ($files as $file){
                $result[] = self::storeOne($file, $type);
            }

            return $result;
        }

        //file array but single file uploaded
        elseif (!empty($_FILES[$key]['name'][0]) && empty($_FILES[$key]['name'][1])){
            $files = self::diverse_array($_FILES[$key]);

            return self::storeOne($files[0], $type);
        }

        //single uploaded file
        else{
            return self::storeOne($_FILES[$key], $type);
        }

    }


    /**
     * @param $file
     * @param $type
     * @return string
     * @throws \Exception
     */
    protected static function storeOne($file, $type)
    {
        $extension = self::getFileExtension($file['name']);

        $fileName = uniqid(self::generateRandomString() );

        $mimeType = self::getFileMimeType($file['tmp_name']);

        $fileSize = $file['size']/(1024 * 1024);

        self::checkFileSize($mimeType, $type, $fileSize);


        $freeSpaceInMg = disk_free_space(self::storagePath($mimeType, $type))/ (1024 * 1024);
        $minSpace      = self::$config['min_storage'];


        if($freeSpaceInMg - $fileName < $minSpace){
            throw new NotEnoughStorageException;
        }


        try{
            $movedFile = self::storagePath($mimeType, $type) . DIRECTORY_SEPARATOR .$fileName.'.'.$extension;

            move_uploaded_file($file['tmp_name'], $movedFile );

            return [
                'path' => $movedFile,
                'size' => $fileSize,
                'mime_type' => $mimeType,
                'file_type' => self::getFileGeneralType($mimeType)
            ];

        }catch(\Exception $e){

            throw new \Exception;
        }
    }


    /**
     *  Return image | audio | video | text
     *
     * @param string $mimeType
     * @return string
     * @throws InvalidMimeTypeException
     */
    public static function getFileGeneralType($mimeType)
    {
        foreach (Image::getMimeTypes() as $key => $value){
            if($mimeType == $value){
                return 'image';
            }
        }

        foreach (Audio::getMimeTypes() as $key => $value){
            if($mimeType == $value){
                return 'audio';
            }
        }


        foreach (Video::getMimeTypes() as $key => $value){
            if($mimeType == $value){
                return 'video';
            }
        }

        foreach (Text::getMimeTypes() as $key => $value){

            if($mimeType == $value){
                return 'text';
            }
        }


        throw new InvalidMimeTypeException;

    }


    /**
     * Check $_FILES[][name]
     *
     * @param (string) $filename - Uploaded file name.
     * @author Yousef Ismaeil Cliprz
     * @return bool
     */
    protected static function checkFileUploadedName ($filename)
    {
        return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i",$filename)) ? true : false);
    }


    /**
     * Check $_FILES[][name] length.
     *
     * @param (string) $filename - Uploaded file name.
     * @author Yousef Ismaeil Cliprz.
     * @return bool
     */
    protected static function check_file_uploaded_length ($filename)
    {
        return (bool) ((mb_strlen($filename,"UTF-8") > 225) ? true : false);
    }


    /**
     * @param $mimeType
     * @param $type
     * @param $fileSize
     * @throws ExceedMaxAllowedFileUpload
     */
    protected static function checkFileSize($mimeType, $type, $fileSize)
    {
        $maxFileSize = min(self::uploadSizeLimit($mimeType, $type), self::file_upload_max_size());

        if($fileSize > $maxFileSize){
            throw new ExceedMaxAllowedFileUpload('Max File Upload Size is '.$maxFileSize.' which you have exceeded it.');
        }
    }


    /**
     * Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
     * @return float|int
     */
    protected static function file_upload_max_size()
    {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $max_size = self::parse_size(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = self::parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size/(1024*1024);
    }

    /**
     * @param $size
     * @return float
     */
    protected static function parse_size($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }



    /**
     * @param $mimeType
     * @param $type
     * @return string
     */
    public static function storagePath($mimeType, $type)
    {
        return self::$config[self::getFileGeneralType($mimeType)][$type]['storagePath'];
    }


    /**
     * @param $mimeType
     * @param $type
     * @return mixed
     */
    public static function uploadSizeLimit($mimeType, $type)
    {
        return self::$config[self::getFileGeneralType($mimeType)][$type]['uploadSizeLimit'];
    }


    /**
     * @param $vector
     * @return array
     */
    protected static function diverse_array($vector)
    {
        $result = [];

        foreach($vector as $key1 => $value1)
            foreach($value1 as $key2 => $value2)
                $result[$key2][$key1] = $value2;

        return $result;
    }


    /**
     * @param int $length
     * @return string
     */
    protected static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
