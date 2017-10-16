<?php
/**
 * post-google-amphtml config file
 * @package post-google-amphtml
 * @version 0.0.1
 * @upgrade true
 */

return [
    '__name' => 'post-google-amphtml',
    '__version' => '0.0.1',
    '__git' => 'https://github.com/getphun/post-google-amphtml',
    '__files' => [
        'modules/post-google-amphtml'    => [ 'install', 'remove', 'update' ],
        'theme/site/post/google-amphtml' => [ 'install', 'remove' ]
    ],
    '__dependencies' => [
        'post',
        'site',
        'site-meta',
        'formatter',
        '/banner'
    ],
    '_services' => [],
    '_autoload' => [
        'classes' => [
            'Camp' => 'modules/post-google-amphtml/third-party/Camp.php',
            'PostGoogleAmphtml\\Controller\\AmpController' => 'modules/post-google-amphtml/controller/AmpController.php',
            'PostGoogleAmphtml\\Meta\\Post' => 'modules/post-google-amphtml/meta/Post.php'
        ],
        'files' => []
    ],
    
    '_routes' => [
        'site' => [
            'sitePostGoogleAmphtml' => [
                'rule' => '/post/amp/:slug',
                'handler' => 'PostGoogleAmphtml\\Controller\\Amp::index'
            ]
        ]
    ]
];