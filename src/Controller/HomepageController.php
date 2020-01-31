<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Cache\Cache;

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
                        'Comments.approved' => true,
                        'Comments.public' => true
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

        // everybody should be able to see the homepage
        $this->Authentication->allowUnauthenticated(['index']);

        // there is no 'Homepage' model so...
        $this->modelClass = false;
    }

    public function index()
    {
        // do some heavy lifting
        $this->setVarsForHomepageAndFeed();

        // if we're logged in, show all the posts
        if ($this->Authentication->getIdentity()) {
            unset($this->paginate['Posts']['conditions']);
            unset($this->paginate['Posts']['contain']['Comments']['conditions']);
        }

        // get the posts!
        $posts = $this->paginate('Posts');

        // tada!
        $this->set([
            'posts' => $posts
        ]);
    }

    public function feed()
    {
        // we don't do much here (it all happens in `ajaxFeed`)
        $this->setVarsForHomepageAndFeed();
    }

    public function ajaxFeed()
    {
        // tell the view to use the ajax layout
        $this->viewBuilder()->setLayout('ajax');

        // load up the friends model
        $this->loadModel('Friends');

        // get all our friends
        $friends = $this->Friends->find()->all();

        // this is where we'll put the posts to display
        $posts = [];

        foreach ($friends as $friend) {
            // get the feed for the friend
            $feed = $friend->getFeed(false);

            // if we've got a feed and it looks good
            if ($feed && isset($feed->items) && $feed->items) {
                // add the posts from the feed into the posts array
                foreach ($feed->items as $item) {
                    $item->friend = $friend;
                    $posts[] = $item;
                }
            }
        }

        // sort all the posts
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

        // paginate the posts
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

        // set some stuff for the view and call it a day
        $this->set([
            'posts' => $paginated,
            'friends' => $friends,
            'pagination' => [
                'total' => count($posts),
                'prev' => $prev,
                'next' => $next
            ]
        ]);
    }

    private function setVarsForHomepageAndFeed()
    {
        // load up some models
        $this->loadModel('Posts');
        $this->loadModel('Medias');
        $this->loadModel('Friends');

        // do this so we can be lazy;
        $authed = $this->Authentication->getIdentity();

        // get some photos for the homepage
        $photos = $this->Medias->find()
            ->where(['Medias.mime LIKE' => 'image/%'])
            ->order(['Medias.created' => 'DESC']);

        // unauthed users can only see public photos
        if (!$authed) {
            $photos = $photos->where(['Medias.public' => true]);
            $photos = $photos->cache('photos', 'homepage_assets');
        }

        // limit to 12 photos... this should probably be a setting.
        $photos = $photos->limit(12)->all();

        // do the same thing we just did for photos, but for videos
        $videos = $this->Medias->find()
            ->where(['Medias.mime LIKE' => 'video/%'])
            ->order(['Medias.created' => 'DESC']);

        if (!$authed) {
            $videos = $videos->where(['Medias.public' => true]);
            $videos = $videos->cache('videos', 'homepage_assets');
        }

        $videos = $videos->limit(12)->all();

        // get 12 (again, probably needs to be a setting) friends
        $friends = $this->Friends->find()
            ->order(['Friends.name' => 'ASC', 'Friends.created' => 'DESC'])
            ->limit(12)
            ->cache('friends', 'homepage_assets')
            ->all();

        // set some variables in the view and let other stuff happen
        $this->set([
            'user' => $this->request->getAttribute('identity'),
            'photos' => $photos,
            'videos' => $videos,
            'friends' => $friends
        ]);
    }
}
