<?php
/**
 * Created by PhpStorm.
 * User: Majid
 * Date: 8/4/2016
 * Time: 3:19 PM
 */
namespace mhndev\media\Formats;
use mhndev\media\File;

/**
 * Class Audio
 * @package mhndev\media\Formats
 */
class Audio extends File
{


    /**
     * @return array
     */
    public static function getMimeTypes()
    {
        return [
            'mp3|m4a|m4b'                  => 'audio/mpeg',
            'ra|ram'                       => 'audio/x-realaudio',
            'wav'                          => 'audio/wav',
            'ogg|oga'                      => 'audio/ogg',
            'mid|midi'                     => 'audio/midi',
            'wma'                          => 'audio/x-ms-wma',
            'wax'                          => 'audio/x-ms-wax',
            'mka'                          => 'audio/x-matroska',
        ];
    }

}
