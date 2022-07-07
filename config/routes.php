<?php
/** @var \Cake\Routing\RouteBuilder $routes */

use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

// Router::defaultRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $rbRoutes) {
    $csrf = new CsrfProtectionMiddleware([
        'httponly' => true
    ]);

    $csrf->skipCheckCallback(function($request) {
        $public = [
            'Users' => ['indieAuth'],
            'Webmentions' => ['add'],
        ];

        $controller = $request->getParam('controller');
        $action = $request->getParam('action');

        if (array_key_exists($controller, $public)) {
            return in_array($action, $public[$controller]);
        }

        return false;
    });

    $rbRoutes->registerMiddleware('csrf', $csrf);

    $rbRoutes->setExtensions(['json', 'xml']);

    $rbRoutes->applyMiddleware('csrf');

    // the homepage
    $rbRoutes->connect('/',
        [
            'controller' => 'Homepage',
            'action' => 'index'
        ],
        [
            '_name' => 'homepage'
        ]
    );

    // news feed (posts from your friends)
    $rbRoutes->connect('/feed',
        [
            'controller' => 'Homepage',
            'action' => 'feed'
        ],
        [
            '_name' => 'feed'
        ]
    );

    $rbRoutes->connect('/feed/{friend_id}',
        [
            'controller' => 'Homepage',
            'action' => 'feed'
        ],
        [
            '_name' => 'friendFeed'
        ]
    )->setPass(['friend_id']);

    $rbRoutes->connect('/feed-item/read/{id}',
        [
            'controller' => 'FeedItems',
            'action' => 'markRead'
        ],
        [
            '_name'=> 'markFeedItemRead'
        ]
    )->setPass(['id']);

    $rbRoutes->connect('/tag/{tag}',
        [
            'controller' => 'Homepage',
            'action' => 'index'
        ],
        [
            '_name'=> 'tag'
        ]
    )->setPass(['tag']);

    // site info
    $rbRoutes->connect('/site-info',
        [
            'controller' => 'Settings',
            'action' => 'siteInfo',
        ],
        [
            '_name' => 'siteInfo',
            '_ext' => 'json'
        ]
    );

    // the regular RSS feed for posts
    $rbRoutes->connect('/rss',
        [
            'controller' => 'Posts',
            'action' => 'feed'
        ],
        [
            '_name' => 'rssFeed',
            '_ext' => 'xml',
        ]
    );

    // the JSON feed for posts
    $rbRoutes->connect('/json',
        [
            'controller' => 'Posts',
            'action' => 'feed'
        ],
        [
            '_name' => 'jsonFeed',
            '_ext' => 'json'
        ]
    );

    $rbRoutes->connect('/friends',
        [
            'controller' => 'Friends',
            'action' => 'index'
        ],
        [
            '_name' => 'friends',
        ]
    );

    $rbRoutes->connect('/add-post',
        [
            'controller' => 'Posts',
            'action' => 'add'
        ],
        [
            '_name' => 'addPost',
        ]
    );

    $rbRoutes->connect('/comments/unapproved',
        [
            'controller' => 'Comments',
            'action' => 'unapproved'
        ],
        [
            '_name' => 'unapprovedComments'
        ]
    );

    // make it easy to get the hero background
    $rbRoutes->connect('/hero-background',
        [
            'controller' => 'Medias',
            'action' => 'heroBackground'
        ]
    );

    // make it easy to get the profile photo
    $rbRoutes->connect('/profile-photo',
        [
            'controller' => 'Medias',
            'action' => 'profilePhoto'
        ],
        [
            '_name' => 'profilePhoto'
        ]
    );

    // view a post
    $rbRoutes->connect('/view-post/{id}',
        [
            'controller' => 'Posts',
            'action' => 'view'
        ],
        [
            '_name' => 'viewPost'
        ]
    )->setPass(['id']);

    // shortcut for viewing photos (/medias/photos)
    $rbRoutes->connect('/photos',
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
    $rbRoutes->connect('/videos',
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
    $rbRoutes->connect('/view-media/{id}',
        [
            'controller' => 'Medias',
            'action' => 'view'
        ],
        [
            '_name' => 'viewMedia'
        ]
    )->setPass(['id']);

    // shortcut for viewing a post
    $rbRoutes->connect('/view-album/{id}',
        [
            'controller' => 'Albums',
            'action' => 'view',
        ],
        [
            '_name' => 'viewAlbum'
        ]
    )->setPass(['id']);

    // shortcut for viewing albums
    $rbRoutes->connect('/albums/{type}',
        [
            'controller' => 'Albums',
            'action' => 'index'
        ],
        [
            '_name' => 'listAlbums'
        ]
    )->setPass(['type']);

    // about page
    $rbRoutes->connect('/about',
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
        'Homepage' => 'homepage',
        'Webmentions' => 'webmentions',
    ];

    foreach ($controllers as $controller => $url) {
        $rbRoutes->connect("/{$url}", ['action' => 'index', 'controller' => $controller], ['routeClass' => DashedRoute::class]);
        $rbRoutes->connect("/{$url}/{action}/*", ['controller' => $controller], ['routeClass' => DashedRoute::class]);
    }

    // pages routing
    $rbRoutes->connect('**', [
        'controller' => 'Pages',
        'action' => 'view'
    ]);
});
