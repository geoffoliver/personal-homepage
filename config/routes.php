<?php

use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httpOnly' => true
    ]));

    $routes->setExtensions(['json', 'xml']);

    $routes->applyMiddleware('csrf');

    // the homepage, which could be posts from the website or a feed of posts
    // from friend's websites, depending on if you're logged in
    $routes->connect('/',
        [
            'controller' => 'Homepage',
            'action' => 'index'
        ]
    );

    // really only used for when you're logged in and you want to see your homepage
    // as visitors to your site see it
    $routes->connect('/homepage',
        [
            'controller' => 'Homepage',
            'action' => 'index', 'homepage'
        ]
    );

    // the regular RSS feed for posts
    $routes->connect('/feed',
        [
            'controller' => 'Homepage',
            'action' => 'index', 'feed'
        ]
    );

    // the JSON feed for posts
    $routes->connect('/page-feed',
        [
            'controller' => 'Posts',
            'action' => 'feed'
        ]
    );

    // make it easy to get the hero background
    $routes->connect('/hero-background',
        [
            'controller' => 'Medias',
            'action' => 'heroBackground'
        ]
    );

    // make it easy to get the profile photo
    $routes->connect('/profile-photo',
        [
            'controller' => 'Medias',
            'action' => 'profilePhoto'
        ]
    );

    // view a post
    $routes->connect('/view-post/:id',
        [
            'controller' => 'Posts',
            'action' => 'view'
        ],
        [
            '_name' => 'viewPost'
        ]
    )->setPass(['id']);

    // shortcut for viewing photos (/medias/photos)
    $routes->connect('/photos',
        [
            'controller' => 'Medias',
            'action' => 'index',
            'photos'
        ],
        [
            '_name' => 'photos'
        ]
    );

    // shortcut for viewing photo album (/medias/videos/{albumId})
    $routes->connect('/photos/:albumId',
        [
            'controller' => 'Medias',
            'action' => 'index',
            'photos'
        ],
        [
            '_name' => 'photoAlbum'
        ]
    )->setPass(['albumId']);

    // shortcut for viewing videos (/medias/videos)
    $routes->connect('/videos',
        [
            'controller' => 'Medias',
            'action' => 'index',
            'videos'
        ],
        [
            '_name' => 'videos'
        ]
    );

    // shortcut for viewing video album (/medias/photos/{albumId})
    $routes->connect('/videos/:albumId',
        [
            'controller' => 'Medias',
            'action' => 'index',
            'videos'
        ],
        [
            '_name' => 'videoAlbum'
        ]
    )->setPass(['albumId']);

    $routes->connect('/medias/view/:id',
        [
            'controller' => 'Medias',
            'action' => 'view'
        ],
        [
            '_name' => 'viewMedia'
        ]
    )->setPass(['id']);

    $routes->fallbacks(DashedRoute::class);
});
