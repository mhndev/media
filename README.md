
## Media Manipulation
simple media manipulation library

```php

define('ROOT', pathinfo(__FILE__)['dirname']);

define('IMAGE_PATH', ROOT.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'image');
define('AUDIO_PATH', ROOT.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'audio');
define('VIDEO_PATH', ROOT.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'video');
define('TEXT_PATH' , ROOT.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'text');



\mhndev\media\UploadFile::config([

    'min_storage' => 100,
    
    'formats'=>[
        'image'=>[
            'avatar'=>[
                'storagePath'=> IMAGE_PATH.DIRECTORY_SEPARATOR.'avatar',
                'uploadSizeLimit' => 2,
            ]
        ],
        'audio'=>[
            'music'=>[
                'storagePath'=> AUDIO_PATH.DIRECTORY_SEPARATOR.'music',
                'uploadSizeLimit' => 10
    
            ],
            'madahi'=>[
                'storagePath'=> AUDIO_PATH.DIRECTORY_SEPARATOR.'madahi',
                'uploadSizeLimit' => 4
    
            ]
        ],
        'video'=>[
            'storagePath'=> VIDEO_PATH,
            'uploadSizeLimit' => 10
        ],
        'text'=>[
            'license'=>[
                'storagePath'=> TEXT_PATH.DIRECTORY_SEPARATOR.'license',
                'uploadSizeLimit' => 1
            ]
        ]
    ]

]);


\mhndev\media\UploadFile::store('text', 'license');
\mhndev\media\UploadFile::store('music', 'music');

```

you can keep your config file as an php array in your config directory and include it in above code.
above code check uploaded files and move input file with text index in rootpath.'/text/license'
and also move input file with music index in rootpath.'/audio/music'