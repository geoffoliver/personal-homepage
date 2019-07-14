<?php
namespace App\Controller;

use App\Controller\AppController;

class HomepageController extends AppController
{
    public $use = [];

    public $paginate = [
        'Posts' => [
            'limit' => 10000,
            'conditions' => [
                'public' => true
            ],
            'contain' => [
                'Medias'
            ]
        ]
    ];

    public function initialize()
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['index']);

        $this->modelClass = false;
    }

    public function index()
    {
        $this->loadModel('Posts');

        $posts = $this->paginate('Posts');

        $this->set([
            'posts' => $posts
        ]);
    }
}
