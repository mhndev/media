<?php
/**
 * Created by PhpStorm.
 * User: Majid
 * Date: 8/4/2016
 * Time: 6:58 PM
 */
namespace mhndev\media;

use mhndev\media\Exceptions\ExceedMaxAllowedFileUpload;
use mhndev\media\Exceptions\NoFileUploadedException;

/**
 * Class UploadFile
 * @package mhndev\media
 */
class UploadFile extends File
{



    /**
     * @param $key
     * @throws NoFileUploadedException
     */
    public static function store($key)
    {
        if(empty($_FILES) || empty($_FILES[$key])){
            throw new NoFileUploadedException;
        }


        //multiple uploaded files and array
        if(!empty($_FILES[$key]['name'][0]) && !empty($_FILES[$key]['name'][0]) ){
            $files = self::diverse_array($_FILES[$key]);

            foreach ($files as $file){
                self::storeOne($file, $key);
            }
        }

        //file array but single file uploaded
        elseif (!empty($_FILES[$key]['name'][0]) && empty($_FILES[$key]['name'][0])){
            $files = self::diverse_array($_FILES[$key]);

            self::storeOne($files[0], $key);
        }

        //single uploaded file
        else{
            self::storeOne($_FILES, $key);
        }

    }


    /**
     * @param $file
     * @param $key
     * @return string
     * @throws \Exception
     */
    protected static function storeOne($file, $key)
    {
        $extension = self::getFileExtension($file['name']);

        $fileName = uniqid(self::generateRandomString() );

        $mimeType = self::getFileMimeType($fileName);

        $fileSize = $file['size']/1024;

        self::checkFileSize($mimeType, $key, $fileSize);

        try{
            $movedFile = self::storagePath($mimeType, $key) . $fileName.'.'.$extension;

            move_uploaded_file($file['tmp_name'], $movedFile );

            return $movedFile;

        }catch(\Exception $e){

            throw new \Exception;
        }
    }


    /**
     * Check $_FILES[][name]
     *
     * @param (string) $filename - Uploaded file name.
     * @author Yousef Ismaeil Cliprz
     * @return bool
     */
    protected function checkFileUploadedName ($filename)
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
    protected function check_file_uploaded_length ($filename)
    {
        return (bool) ((mb_strlen($filename,"UTF-8") > 225) ? true : false);
    }


    protected function checkFileSize($mimeType, $key, $fileSize)
    {
        $maxFileSize = min(self::uploadSizeLimit($mimeType, $key), $this->file_upload_max_size());

        if($fileSize > $maxFileSize){
            throw new ExceedMaxAllowedFileUpload('Max File Upload Size is '.$maxFileSize.' which you have exceeded it.');
        }
    }


    /**
     * Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
     * @return float|int
     */
    protected function file_upload_max_size()
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
        return $max_size;
    }

    /**
     * @param $size
     * @return float
     */
    protected function parse_size($size)
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
     * @param $key
     * @return string
     */
    public static function storagePath($mimeType, $key)
    {
        return self::$config[$mimeType][$key]['storagePath'];
    }


    /**
     * @param $mimeType
     * @param $key
     * @return mixed
     */
    public static function uploadSizeLimit($mimeType, $key)
    {
        return self::$config[$mimeType][$key]['uploadSizeLimit'];
    }


    /**
     * @param $vector
     * @return array
     */
    protected function diverse_array($vector)
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
    protected function generateRandomString($length = 10)
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
