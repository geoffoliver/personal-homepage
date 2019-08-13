<?php
namespace App\Controller;

use Cake\Utility\Hash;
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
            ],
            'order' => [
              'Posts.created' => 'DESC'
            ]
        ]
    ];

    public function initialize()
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['index']);

        $this->modelClass = false;
    }

    public function index($view = null)
    {
        // is there a user logged in?
        $user = $this->request->getAttribute('identity');

        // no user? nothing special to do. just show the homepage and be done.
        if (!$user) {
            return $this->homepage();
        }

        // make sure the view being requested is valid
        $views = ['homepage', 'feed'];
        if ($view && !in_array($view, $views)) {
            throw new \Exception('Invalid request');
        }

        // if we're logged in and we haven't specified which page we want to see,
        // then show the feed because why would you want to see your own
        // homepage by default, right?
        if (!$view) {
            $view = 'feed';
        }

        // display something
        switch ($view) {
            case 'homepage':
                return $this->homepage();
            case 'feed':
                return $this->feed();
        }

        // if we're still here things have gone terribly wrong.
        throw new \Exception('Invalid request');
    }

    public function homepage()
    {
        $this->loadModel('Posts');
        $this->loadModel('Medias');
        $this->loadModel('Friends');

        $posts = $this->paginate('Posts');

        $medias = $this->Medias->find()
          ->order(['Medias.created' => 'DESC'])
          ->limit(12);

        $photos = $medias->where(['Medias.mime LIKE' => 'image/%'])->all();
        $videos = $medias->where(['Medias.mime LIKE' => 'video/%'])->all();

        $friends = $this->Friends->find()
            ->order(['Friends.created' => 'DESC'])
            ->limit(12)
            ->all();

        $this->set([
            'user' => $this->request->getAttribute('identity'),
            'posts' => $posts,
            'photos' => $photos,
            'videos' => $videos,
            'friends' => $friends
        ]);

        return $this->render('homepage');
    }

    public function feed()
    {
        $this->loadModel('Friends');

        $friends = $this->Friends->find()->order(['Friends.created' => 'DESC'])->all();

        $this->set([
            'friends' => $friends,
            'posts' => [],
            'user' => $this->request->getAttribute('identity')
        ]);

        return $this->render('feed');
    }

    public function ajaxFeed()
    {
        $this->loadModel('Friends');
        $this->layout = 'ajax';

        $friends = $this->Friends->find()->all();

        $posts = [];

        foreach ($friends as $friend) {
            $feed = $friend->getFeed(false);
            if ($feed && isset($feed->items) && $feed->items) {
                foreach ($feed->items as $item) {
                    $item->friend = $friend;
                    $posts[]= $item;
                }
            }
        }

        usort($posts, function($a, $b) {
            if (
                !$a->date_published ||
                !$b->date_published ||
                $a->date_published == $b->date_published
            ) {
                return 0;
            }

            return $a->date_published->gt($b->date_published) ? -1 : 1;
        });

        $this->set(['posts' => $posts]);
    }

}
