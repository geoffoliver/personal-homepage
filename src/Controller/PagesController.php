<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;

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

        $pageConfig->close();

        $content = $pageFile->read();

        $pageFile->close();

        $this->set([
            'pageContent' => $content,
            'config' => $config
        ]);
    }
}
