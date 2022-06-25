<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Exception\NotFoundException;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 *
 * @method \App\Model\Entity\Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostsController extends AppController
{

    private $parsedown;

    public function initialize(): void
    {
        parent::initialize();

        $this->parsedown = new \Parsedown();
        $this->parsedown->setStrictMode(true);

        $this->Authentication->allowUnauthenticated(['feed', 'view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    /*
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Medias']
        ];
        $posts = $this->paginate($this->Posts);

        $this->set(compact('posts'));
    }
    */

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // get the post
        $post = $this->Posts->find()
            ->where([
                'Posts.id' => $id,
            ]);

        if ($this->Authentication->getIdentity()) {
            // if user is authed, get all the comments
            $post = $post->contain([
                'Medias.Comments',
                'Comments',
                'Users'
            ]);
        } else {
            // unauthed users can only see public+approved comments
            $post = $post->where([
                'Posts.public' => true
            ])->contain([
                'Users',
                'Medias' => [
                    'conditions' => [
                        'Medias.public' => true
                    ],
                    'Comments' => [
                        'conditions' => [
                            'Comments.approved' => true,
                            'Comments.public' => true
                        ]
                    ],
                ],
                'Comments' => [
                    'conditions' => [
                        'Comments.approved' => true,
                        'Comments.public' => true
                    ]
                ]
            ]);
        }

        $post = $post->first();

        if (!$post) {
            $this->Flash->error(__('Invalid post.'));
            return $this->redirect('/');
        }

        // set the post in the view and we're done
        $this->set('post', $post);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $post = $this->Posts->newEmptyEntity();
        $user = $this->request->getAttribute('identity');
        $saved = false;
        $sharing = $this->request->getParam('action') === "share";

        if ($this->request->is('post')) {
            // make some post data
            $postData = [
                'name' => $this->request->getData('name'),
                'content' => $this->request->getData('content'),
                'source' => $this->request->getData('source'),
                'public' => $this->request->getData('public'),
                'allow_comments' => $this->request->getData('allow_comments'),
                'user_id' => $user->id,
                'is_link' => $this->request->getData('is_link'),
                'show_embeds' => $this->request->getData('show_embeds'),
            ];

            // try to create/save the post
            $post = $this->Posts->patchEntity(
                $post,
                $postData,
                ['associated' => ['Medias']]
            );

            if ($this->Posts->save($post)) {
                // if there's media, attach (link) the media to the post
                if ($att = $this->request->getData('new_media')) {
                    foreach ($att as $attId) {
                        $media = $this->Posts->Medias->find()
                            ->where([
                                'Medias.id' => $attId,
                                'Medias.post_id IS NULL'
                            ])
                            ->first();

                        if (!$media) {
                            continue;
                        }

                        $this->Posts->Medias->link($post, [$media]);
                    }
                }

                // we're all good!
                $this->Flash->success(__('The post has been {0}.', $sharing ? 'shared' : 'saved'));

                if ($sharing) {
                    // if we're sharing, just stay on the page
                    $saved = true;
                } else {
                    // just a regular add/edit, send the user off the viewPost page
                    return $this->redirect(['_name' => 'viewPost', $post->id]);
                }
            } else {
                // ugh, this is no good.
                $this->Flash->error(__('The post could not be saved. Please, try again.'));
            }
        } else {
            $post->name = urldecode($this->request->getQuery('name', ''));
            $post->source = urldecode($this->request->getQuery('source', ''));
            $post->is_link = urldecode($this->request->getQuery('isLink', false));

            if ($body = $this->request->getQuery('body')) {
                $post->content = urldecode($body);
            } else {
                $post->content = $post->source;
            }

            if ($post->content) {
                $post->content = "> {$post->content}";
            }

            if ($post->name) {
                $post->name = "Shared: {$post->name}";
            }

        }

        $this->set(compact('post', 'saved', 'sharing'));
    }

    public function share()
    {
        // all this does is change the layout, then call the `add` method
        $this->viewBuilder()->setLayout('simple');
        $this->add();
    }

    /**
     * Edit method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => [
                'Medias'
            ]
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $postData = [
                'name' => $this->request->getData('name'),
                'content' => $this->request->getData('content'),
                'source' => $this->request->getData('source'),
                'public' => $this->request->getData('public'),
                'allow_comments' => $this->request->getData('allow_comments'),
                'medias' => $this->request->getData('medias', []),
                'is_link' => $this->request->getData('is_link'),
                'show_embeds' => $this->request->getData('show_embeds'),
                'embeds' => $this->request->getData('embeds'),
            ];

            $post = $this->Posts->patchEntity($post, $postData);

            if ($this->Posts->save($post)) {
                if ($att = $this->request->getData('new_media')) {
                    foreach ($att as $attId) {
                        $media = $this->Posts->Medias->find()
                            ->where([
                                'Medias.id' => $attId,
                                'Medias.post_id IS NULL'
                            ])
                            ->first();

                        if (!$media) {
                            continue;
                        }

                        $this->Posts->Medias->link($post, [$media]);
                    }
                }

                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['_name' => 'viewPost', $post->id]);
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }

        $users = $this->Posts->Users->find('list', ['limit' => 200]);
        $this->set(compact('post', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $post = $this->Posts->get($id);

        if (!$post) {
            throw new NotFoundException(__('Invalid post'));
        }

        if ($this->Posts->delete($post)) {
            $this->Flash->success(__('The post has been deleted.'));
        } else {
            $this->Flash->error(__('The post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['_name' => 'homepage']);
    }

    public function feed()
    {
        // setup the data we're gonna pull
        $this->paginate = [
            'Posts' => [
                'limit' => 100,
                'maxLimit' => 100,
                'conditions' => [
                    'Posts.public' => true
                ],
                'contain' => [
                    'Users',
                    'Medias' => [
                        'conditions' => [
                            'Medias.public' => true
                        ]
                    ],
                    'Comments' => [
                        'conditions' => [
                            'Comments.approved' => true,
                            'Comments.public' => true
                        ]
                    ]
                ],
                'order' => [
                    'Posts.modified' => 'DESC'
                ]
            ]
        ];

        // grab some posts
        try {
            $posts = $this->paginate($this->Posts);
        } catch (\Exception $ex) {
            // we should probably handle this?
            $posts = [];
        }

        if ($this->request->is('json')) {
            // output a JSON feed
            $this->setJsonFeedVars($posts);
            return;
        }

        if ($this->request->is('xml')) {
            // output an RSS feed
            $this->setRssFeedVars($posts);
            return;
        }

        // if we're still here, something has gone terribly wrong.
        // throw an exception and call it a day
        throw new \Exception('Invalid request');
    }

    private function setRssFeedVars($posts)
    {
        $this->set('posts', $posts);
        // $this->response->type(['atom' => 'application/xml']);
        // $this->response = $this->response->withType('atom');
        return $this->response;
    }

    /**
     * Sets necessary view variables for a JSON feed.
     * Should be compatible with spec defined at https://jsonfeed.org/version/1
     *
     * @param Cake\ORM\ResultSet $posts Collection of posts
     *
     * @return void
     */
    private function setJsonFeedVars($posts)
    {
        $paging = null;

        // get the paging data so we can generate a `next_url`
        if ($p = $this->request->getAttribute('paging')) {
            $paging = $p['Posts'];
        }

        $homepageUrl = Router::url([
            '_name' => 'homepage',
        ], true);

        $feedUrl = Router::url([
            '_name' => 'jsonFeed',
            '_ext' => 'json'
        ], true);

        $iconUrl = Router::url([
            '_name' => 'profilePhoto'
        ], true);

        $nextUrl = null;
        if ($paging && $paging['pageCount'] > $paging['page']) {
            $nextUrl = Router::url([
                '_name' => 'jsonFeed',
                '_ext' => 'json',
                'page' => $paging['page'] + 1
            ], true);
        }

        // set the variables for the JSON feed
        $this->set([
            'version' => 'https://jsonfeed.org/version/1',
            'title' => Hash::get($this->settings, 'site-title'),
            'description' => Hash::get($this->settings, 'homepage-about'),
            'home_page_url' => $homepageUrl,
            'feed_url' => $feedUrl,
            'next_url' => $nextUrl,
            'icon' => $iconUrl,
            'favicon' => $iconUrl,
            'author' => [
                'name' => Hash::get($this->settings, 'site-name'),
                'url' => $homepageUrl,
                'avatar' => $iconUrl
            ],
            'items' => $this->postsToJsonFeedItems($posts),
            '_serialize' => [
                'version',
                'title',
                'description',
                'home_page_url',
                'feed_url',
                'next_url',
                'icon',
                'favicon',
                'author',
                'items'
            ]
        ]);
    }

    /**
     * Converts a Post to a JSON feed compatible item
     *
     * @param Cake\ORM\ResultSet $posts A collection of posts
     *
     * @return Array An array of items suitable for use in the 'items' of a JSON feed
     */
    private function postsToJsonFeedItems($posts)
    {
        // if there aren't any posts, hand back an empty array
        if (!$posts->count()) {
            return [];
        }

        // this is where the posts will live
        $jsonFeedPosts = [];

        // looop over the posts and create the items for the feed
        foreach ($posts as $post) {
            // create the text version of the post content
            $contentText = strip_tags($post->content);

            // create the HTML version of the post
            $contentHtml = $this->parsedown->text($post->content);

            $postUrl = Router::url([
                '_name' => 'viewPost',
                $post->id
            ], true);

            // generate the item feed
            $postItem = [
                'id' => $post->id,
                'url' => $postUrl,
                'title' => $post->name,
                'content_html' => $contentHtml,
                'content_text' => $contentText,
                'summary' => substr($contentText, 0, 512),
                //'image' => 'post-image',
                //'banner_image' => 'post-banner-image',
                'date_published' => $post->created,
                'date_modified' => $post->modified,
                '_page_feed' => [
                    'about' => 'Custom fields for PageFeed',
                    'comments' => [
                        'url' => $postUrl . '#comments',
                        'total' => count($post->comments)
                    ]
                ]
            ];

            // if there's media associated with the post, use it to populate
            // the 'attachments' field on the item
            if ($post->medias) {
                $postItem['attachments'] = [];
                foreach ($post->medias as $media) {
                    $postItem['attachments'][]= [
                        'url' => Router::url([
                            'controller' => 'Medias',
                            'action' => 'download',
                            $media->id
                        ], true),
                        'mime_type' => $media->mime,
                        'title' => $media->name,
                        'size_in_bytes' => $media->size
                    ];
                }
                $postItem['image'] = $postItem['attachments'][0]['url'];
            }

            // if there's a 'source' on the post, use it to populate the
            // 'external_url' field on the item
            if ($src = $post->source) {
                $postItem['external_url'] = $src;
            }

            $jsonFeedPosts[]= $postItem;
        }

        return $jsonFeedPosts;
    }
}
