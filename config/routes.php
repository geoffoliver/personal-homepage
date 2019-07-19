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

    $routes->setExtensions(['json','xml']);

    $routes->applyMiddleware('csrf');

    $routes->connect('/', ['controller' => 'Homepage', 'action' => 'index']);
    $routes->connect('/homepage', ['controller' => 'Homepage', 'action' => 'index', 'homepage']);
    $routes->connect('/feed', ['controller' => 'Homepage', 'action' => 'index', 'feed']);

    $routes->connect('/hero-background', ['controller' => 'Medias', 'action' => 'heroBackground']);
    $routes->connect('/profile-photo', ['controller' => 'Medias', 'action' => 'profilePhoto']);

    $routes->fallbacks(DashedRoute::class);
});
