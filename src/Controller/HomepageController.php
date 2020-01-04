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
                'Comments' => [
                    'conditions' => [
                        'Comments.approved' => true
                    ]
                ]
            ],
            'order' => [
                'Posts.modified' => 'DESC'
            ],
            'limit' => 50
        ]
    ];

    public function initialize()
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['index', 'ajaxHomepage']);

        $this->modelClass = false;
    }

    public function index($view = null)
    {
        $this->setVarsForHomepageAndFeed();

        $authed = false;

        if ($this->Authentication->getIdentity()) {
            unset($this->paginate['Posts']['conditions']);
            $authed = true;
        }

        $posts = $this->paginate('Posts');

        $this->set([
            'posts' => $posts,
            'authed' => $authed
        ]);
    }

    private function setVarsForHomepageAndFeed()
    {
        $this->loadModel('Posts');
        $this->loadModel('Medias');
        $this->loadModel('Friends');
        $authed = false;

        if ($this->Authentication->getIdentity()) {
            $authed = true;
        }

        $photos = $this->Medias->find()
            ->where(['Medias.mime LIKE' => 'image/%'])
            ->order(['Medias.created' => 'DESC']);

        if (!$authed) {
            $photos = $photos->where(['Medias.public' => true]);
            $photos = $photos->cache('photos', 'homepage_assets');
        }

        $photos = $photos->limit(12)->all();

        $videos = $this->Medias->find()
            ->where(['Medias.mime LIKE' => 'video/%'])
            ->order(['Medias.created' => 'DESC']);

        if (!$authed) {
            $videos = $videos->where(['Medias.public' => true]);
            $videos = $videos->cache('videos', 'homepage_assets');
        }

        $videos = $videos->limit(12)->all();

        $friends = $this->Friends->find()
            ->order(['Friends.name' => 'ASC'])
            ->limit(12)
            ->cache('friends', 'homepage_assets')
            ->all();

        $this->set([
            'user' => $this->request->getAttribute('identity'),
            'photos' => $photos,
            'videos' => $videos,
            'friends' => $friends
        ]);
    }

    public function feed()
    {
        $this->setVarsForHomepageAndFeed();
    }

    public function ajaxFeed()
    {
        $this->loadModel('Friends');
        $this->viewBuilder()->setLayout('ajax');

        $friends = $this->Friends->find()->all();

        $posts = [];

        foreach ($friends as $friend) {
            $feed = $friend->getFeed(false);
            if ($friend->name === 'Boing Boing') {
                dd($feed);
            }
            if ($feed && isset($feed->items) && $feed->items) {
                foreach ($feed->items as $item) {
                    $item->friend = $friend;
                    $posts[] = $item;
                }
            }
        }

        usort($posts, function ($a, $b) {
            if (
                !$a->date_published ||
                !$b->date_published ||
                $a->date_published == $b->date_published
            ) {
                return 0;
            }

            return $a->date_published->gt($b->date_published) ? -1 : 1;
        });

        $limit = 50;
        $page = 1;
        if ($this->request->getQuery('page')) {
            $page = $this->request->getQuery('page');
        }

        $page = (int) $page;

        $paginated  = array_slice($posts, ($page - 1) * $limit, $limit);

        $prev = null;
        $next = null;

        if ($page > 1) {
            $prev = $page - 1;
        }

        if (count($posts) > $page * $limit) {
            $next = $page + 1;
        }

        $this->set([
            'posts' => $paginated,
            'pagination' => [
                'total' => count($posts),
                'prev' => $prev,
                'next' => $next
            ]
        ]);
    }
}
