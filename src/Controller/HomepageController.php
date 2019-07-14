<?php
namespace App\Controller;

use App\Controller\AppController;

class HomepageController extends AppController
{
    public $use = [];

    public $paginate = [
        'Posts' => [
            'conditions' => [
                'public' => true
            ],
            'contain' => [
                'Medias',
                'Comments'
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
        $this->loadModel('Medias');
        $this->loadModel('Friends');

        $posts = $this->paginate('Posts');

        $photos = $this->Medias->find()
          ->where(['Medias.mime LIKE' => 'image/%'])
          ->order(['Medias.created' => 'DESC'])
          ->limit(12)
          ->all();

        $videos = $this->Medias->find()
          ->where(['Medias.mime LIKE' => 'video/%'])
          ->order(['Medias.created' => 'DESC'])
          ->limit(12)
          ->all();

        $friends = $this->Friends->find()
            ->limit(12)
            ->all();

        $this->set([
            'posts' => $posts,
            'photos' => $photos,
            'videos' => $videos,
            'friends' => $friends
        ]);
    }
}
