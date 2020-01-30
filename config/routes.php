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

    // the homepage
    $routes->connect('/',
        [
            'controller' => 'Homepage',
            'action' => 'index'
        ],
        [
            '_name' => 'homepage'
        ]
    );

    // news feed (posts from your friends)
    $routes->connect('/feed',
        [
            'controller' => 'Homepage',
            'action' => 'feed'
        ],
        [
            '_name' => 'feed'
        ]
    );

    $routes->connect('/friends',
        [
            'controller' => 'Friends',
            'action' => 'index'
        ],
        [
            '_name' => 'friends',
        ]
    );

    $routes->connect('/add-post',
        [
            'controller' => 'Posts',
            'action' => 'add'
        ],
        [
            '_name' => 'addPost',
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

    // shortcut for viewing a media item
    $routes->connect('/view-media/:id',
        [
            'controller' => 'Medias',
            'action' => 'view'
        ],
        [
            '_name' => 'viewMedia'
        ]
    )->setPass(['id']);

    // shortcut for viewing a post
    $routes->connect('/view-album/:id',
        [
            'controller' => 'Albums',
            'action' => 'view',
        ],
        [
            '_name' => 'viewAlbum'
        ]
    )->setPass(['id']);

    // shortcut for viewing albums
    $routes->connect('/albums/:type',
        [
            'controller' => 'Albums',
            'action' => 'index'
        ],
        [
            '_name' => 'listAlbums'
        ]
    )->setPass(['type']);

    // about page
    $routes->connect('/about',
        [
            'controller' => 'About',
            'action' => 'index'
        ],
        [
            '_name' => 'about',
        ]
    );

    // wire up all the controllers
    $controllers = [
        'Albums' => 'albums',
        'Comments' => 'comments',
        'Friends' => 'friends',
        'Medias' => 'medias',
        'Posts' => 'posts',
        'Settings' => 'settings',
        'Users' => 'users',
        'Homepage' => 'homepage'
    ];

    foreach ($controllers as $controller => $url) {
        $routes->connect("/{$url}", ['action' => 'index', 'controller' => $controller], ['routeClass' => DashedRoute::class]);
        $routes->connect("/{$url}/:action/*", ['controller' => $controller], ['routeClass' => DashedRoute::class]);
    }

    // pages routing
    $routes->connect('**', [
        'controller' => 'Pages',
        'action' => 'view'
    ]);
});
