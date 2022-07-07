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
                'Users',
                'Medias',
                'Comments' => [
                    'conditions' => [
                        'Comments.approved' => true,
                        'Comments.public' => true
                    ]
                ],
            ],
            'order' => [
                'Posts.modified' => 'DESC'
            ],
            'limit' => 50
        ]
    ];

    public function initialize(): void
    {
        parent::initialize();

        // everybody should be able to see the homepage
        $this->Authentication->allowUnauthenticated(['index']);

        // there is no 'Homepage' model so...
        $this->modelClass = false;
    }

    public function index($tag = null)
    {
        // do some heavy lifting
        $this->setVarsForHomepageAndFeed();

        // if we're logged in, show all the posts
        if ($this->Authentication->getIdentity()) {
            unset($this->paginate['Posts']['conditions']);
            unset($this->paginate['Posts']['contain']['Comments']['conditions']);
        }

        if ($tag) {
            $tag = trim($tag);
            if ($tag) {
                // filter posts by tag (hashtag)
                list ($tag) = explode(' ', $tag);
                $conditions = isset($this->paginate['Posts']['conditions']) ? $this->paginate['Posts']['conditions'] : [];
                $this->paginate['Posts']['conditions'] = array_merge($conditions, [
                    'Posts.content LIKE' => "%#{$tag}%"
                ]);
            }
        };

        try {
            // get the posts!
            $posts = $this->paginate('Posts');
        } catch (\Exception $ex) {
            return $this->redirect('/');
        }

        // tada!
        $this->set([
            'posts' => $posts,
            'tag' => $tag,
        ]);
    }

    public function feed($friendId = null)
    {
        // we don't do much here (it all happens in `ajaxFeed`)
        $this->setVarsForHomepageAndFeed($friendId);
    }

    public function ajaxFeed()
    {
        // tell the view to use the ajax layout
        $this->viewBuilder()->setLayout('ajax');

        $this->FeedItems = $this->fetchTable('FeedItems');
        $this->Friends = $this->fetchTable('Friends');

        // get all our friends
        $hasFriends = $this->Friends->find()->count() > 0;

        // paginate the posts
        $limit = 50;
        $page = 1;
        if ($this->request->getQuery('page')) {
            $page = $this->request->getQuery('page');
        }

        $page = (int) $page;

        $where = null;

        $friendId = $this->request->getQuery('friend');
        if ($friendId) {
            $where = [
                'friend_id' => $friendId,
            ];
        }

        $posts = $this->FeedItems->find()
            ->contain(['Friends'])
            ->where($where)
            ->limit($limit)
            ->order(['date_modified' => 'DESC'])
            ->page($page);

        $prev = null;
        $next = null;

        if ($page > 1) {
            $prev = $page - 1;
        }

        $hasNext = $this->FeedItems->find()
            ->where($where)
            ->limit($limit)
            ->page($page + 1)
            ->count() > 0;

        $next = $hasNext ? $page + 1 : null;

        // set some stuff for the view and call it a day
        $this->set([
            'posts' => $posts->all(),
            'hasFriends' => $hasFriends,
            'pagination' => [
                'prev' => $prev,
                'next' => $next
            ]
        ]);
    }

    private function setVarsForHomepageAndFeed($friendId = null)
    {
        $this->set([
            'user' => $this->request->getAttribute('identity'),
            'friends' => $this->fetchTable('Friends')->find()->orderAsc('name')->all(),
            'friendId' => $friendId,
        ]);
    }
}
