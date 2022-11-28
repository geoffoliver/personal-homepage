<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    protected $settings = [];

    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->addHelpers([
            'Authentication.Identity',
            'Form' => [
                'templates' => 'bulma_form'
            ],
            'Paginator' => [
                'templates' => 'bulma_pagination'
            ],
        ]);

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication', [
            'logoutRedirect' => '/users/login'
        ]);

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        // grab the settings so we can get at them easily from the views
        $this->Settings = $this->fetchTable('Settings');

        $set = $this->Settings->find()->all();

        foreach ($set as $s) {
            $this->settings[$s->name] = $s->value;
        }

        $this->set('settings', $this->settings);

        return parent::beforeFilter($event);

    }
}
