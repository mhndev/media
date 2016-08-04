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
 * Class Video
 * @package mhndev\media\Formats
 */
class Video extends File
{

    /**
     * @return array
     */
    public static function getMimeTypes()
    {
        return [
            'asf|asx'                      => 'video/x-ms-asf',
            'wmv'                          => 'video/x-ms-wmv',
            'wmx'                          => 'video/x-ms-wmx',
            'wm'                           => 'video/x-ms-wm',
            'avi'                          => 'video/avi',
            'divx'                         => 'video/divx',
            'flv'                          => 'video/x-flv',
            'mov|qt'                       => 'video/quicktime',
            'mpeg|mpg|mpe'                 => 'video/mpeg',
            'mp4|m4v'                      => 'video/mp4',
            'ogv'                          => 'video/ogg',
            'webm'                         => 'video/webm',
            'mkv'                          => 'video/x-matroska',
        ];
    }

}
