<?php
namespace App;

use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Http\Middleware\EncryptedCookieMiddleware;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application extends BaseApplication implements AuthenticationServiceProviderInterface
{
    public function bootstrap()
    {
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }

        if (Configure::read('debug')) {
            $this->addPlugin(\DebugKit\Plugin::class);
        }

        $this->addPlugin('Authentication');

        $this->addPlugin('WyriHaximus/MinifyHtml', ['bootstrap' => true]);
    }

    public function middleware($middlewareQueue)
    {

        $cookies = new EncryptedCookieMiddleware(['secrets'], Configure::read('Security.cookieKey'));

        $middlewareQueue
            ->add(new ErrorHandlerMiddleware(null, Configure::read('Error')))
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime')
            ]))
            ->add(new RoutingMiddleware($this))
            ->add(new AuthenticationMiddleware($this))
            ->add($cookies);

        return $middlewareQueue;
    }

    protected function bootstrapCli()
    {
        try {
            $this->addPlugin('Bake');
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }

        $this->addPlugin('Migrations');
    }

    public function getAuthenticationService(ServerRequestInterface $request, ResponseInterface $response)
    {
        $service = new AuthenticationService();

        $fields = [
            'username' => 'email',
            'password' => 'password'
        ];

        $service->setConfig([
            'unauthenticatedRedirect' => '/users/login',
            'queryParam' => 'redirect'
        ]);

        $service->loadIdentifier('Authentication.Password', ['fields' => $fields]);
        $service->loadAuthenticator('Authentication.Session');
        $service->loadAuthenticator('Authentication.Cookie', [
            'fields' => $fields,
            'cookie' => [
                'secure' => true,
                'httpOnly' => true,
            ]
        ]);
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
            'loginUrl' => [
                '/users/login',
                '/users/indie-auth'
            ]
        ]);

        return $service;
    }
}
