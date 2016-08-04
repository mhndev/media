<?php
/**
 * Created by PhpStorm.
 * User: Majid
 * Date: 8/4/2016
 * Time: 3:20 PM
 */
namespace mhndev\media\Formats;
use mhndev\media\File;

/**
 * Class Text
 * @package mhndev\media\Formats
 */
class Text extends File
{
    /**
     * @return array
     */
    public static function getMimeTypes()
    {
        return [
            'txt|asc|c|cc|h'               => 'text/plain',
            'csv'                          => 'text/csv',
            'tsv'                          => 'text/tab-separated-values',
            'ics'                          => 'text/calendar',
            'rtx'                          => 'text/richtext',
            'css'                          => 'text/css',
            'htm|html'                     => 'text/html',
            'excel'                        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
    }
}
