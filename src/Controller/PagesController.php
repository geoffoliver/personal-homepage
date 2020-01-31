<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Hash;

/**
 * Pages Controller
 */
class PagesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        // let everybody access a few methods
        $this->Authentication->allowUnauthenticated([
            'view',
        ]);
    }

    public function view($page)
    {
        $pagesDir = ROOT . DS . 'pages';

        if (!file_exists($pagesDir)) {
            throw new \Exception(__('Pages directory does not exist.'));
        }

        if (!is_dir($pagesDir)) {
            throw new \Exception(__('Pages directory does is not a directory.'));
        }

        $page = trim($page, '/');

        $pageFile = new File($pagesDir . DS . $page . '.html');

        if (!$pageFile->exists()) {
            $this->Flash->error(__('Invalid page.'));
            return $this->redirect('/');
        }

        $pageConfig = new File($pagesDir . DS . $page . '.config.json');
        $config = new \stdClass();
        if ($pageConfig->exists()) {
            $config = json_decode($pageConfig->read());
        }

        if (isset($config->layout)) {
            $this->viewBuilder()->setLayout($config->layout);
        }

        if (isset($config->auth)) {
            $this->authorize($config->auth);
        }

        $pageConfig->close();

        $content = $pageFile->read();

        $pageFile->close();

        $this->set([
            'pageContent' => $content,
            'config' => $config
        ]);
    }

    private function authorize($auth)
    {
        $authUser = env('PHP_AUTH_USER');
        $authPw = env('PHP_AUTH_PW');

        if (
            $authUser &&
            $authPw &&
            $authUser == $auth->username &&
            $authPw == $auth->password
        ) {
            return true;
        }

        HEADER('WWW-Authenticate: Basic realm="Login to view page"');
        HEADER('HTTP/1.0 401 Unauthorized');
        echo "You must login to view this page.";
        exit;
    }
}
