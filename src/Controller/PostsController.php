<?php
namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Utility\Text;

use nadar\quill\Lexer;
use nadar\quill\Debug;
use nadar\quill\listener\Image;
use nadar\quill\listener\Video;

use App\Lib\Quill\Listeners\CodeBlock;
// use App\Lib\Quill\Listeners\CustomImage;
use App\Controller\AppController;

/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 *
 * @method \App\Model\Entity\Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['feed', 'view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Medias']
        ];
        $posts = $this->paginate($this->Posts);

        $this->set(compact('posts'));
    }

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $contain = [
            'Users',
            'Comments' => [
                'sort' => [
                    'Comments.created' => 'DESC'
                ]
            ]
        ];

        if ($this->Authentication->getIdentity()) {
            $contain[]= 'Medias.Comments';
        } else {
            $contain['Medias.Comments'] = [
                'conditions' => [
                    'Comments.approved' => true
                ]
            ];
            $contain['Comments']['conditions'] = [
                'Comments.approved' => true
            ];
        }


        $post = $this->Posts->get($id, [
            'contain' => $contain
        ]);

        $this->set('post', $post);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $post = $this->Posts->newEntity();
        $user = $this->request->getAttribute('identity');

        if ($this->request->is('post')) {
            $post = $this->Posts->patchEntity(
                $post,
                $this->request->getData(),
                ['associated' => ['Medias']]
            );

            $post->user_id = $user->id;
            /*
            $quillDelta = $this->request->getData('delta');
            $lexer = $this->getLexer($quillDelta);
            $post->content = str_replace('<p><br></p>', '', $lexer->render());
            */

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

        $this->set(compact('post'));
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
            $post = $this->Posts->patchEntity($post, $this->request->getData());

            if (!$this->request->getData('medias')) {
                $post->medias = [];
            }

            /*
            $quillDelta = $this->request->getData('delta');
            $lexer = $this->getLexer($quillDelta);
            $post->content = str_replace('<p><br></p>', '', $lexer->render());
            */
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
        if ($this->Posts->delete($post)) {
            $this->Flash->success(__('The post has been deleted.'));
        } else {
            $this->Flash->error(__('The post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller' => 'Homepage', 'action' => 'homepage']);
    }

    public function feed()
    {
        // setup the data we're gonna pull
        $this->paginate = [
            'contain' => ['Users', 'Medias', 'Comments']
        ];

        // grab some posts
        $posts = $this->paginate($this->Posts);

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
        die('Not implemented');
    }

    private function postsToRssFeedItems($posts)
    {

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
        // get the paging data so we can generate a `next_url`
        $paging = $this->request->paging['Posts'];

        // set the variables for the JSON feed
        $this->set([
            'version' => 'https://jsonfeed.org/version/1',
            'title' => 'Feed name',
            'description' => 'Feed description',
            'home_page_url' => 'home-page-url',
            'feed_url' => 'feed-url',
            'next_url' => 'next-url',
            'icon' => 'icon',
            'favicon' => 'favicon',
            'author' => [
                'name' => 'author-name',
                'url' => 'author-url',
                'avatar' => 'author-avatar'
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

            // generate the item feed
            $postItem = [
                'id' => $post->id,
                'url' => $post->url_alias,
                'title' => $post->name,
                'content_html' => $post->content,
                'content_text' => $contentText,
                'summary' => substr($contentText, 0, 512),
                'image' => 'post-image',
                'banner_image' => 'post-banner-image',
                'date_published' => $post->created,
                'date_modified' => $post->modified,
                '_page_feed' => [
                    'about' => 'Custom fields for PageFeed',
                    'comments' => [
                        'url' => 'comments-url',
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
                        'url' => 'att-url',
                        'mime_type' => $media->mime,
                        'title' => $media->name,
                        'size_in_bytes' => $media->size
                    ];
                }
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

    private function getLexer($quillDelta)
    {
        $lexer = new Lexer($quillDelta);
        $lexer->escapeInput = true;

        // setup listeners
        $lexer->registerListener(new CodeBlock);
        // $lexer->registerListener(new CustomImage);

        // fix image wrapper
        $image = new Image();
        $image->wrapper = '<img src="{src}" />';
        $lexer->registerListener($image);

        // fix video wrapper
        $video = new Video();
        $video->wrapper = '<figure class="image is-16by9"><iframe src="{url}" class="has-ratio" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></figure>';
        $lexer->registerListener($video);

        return $lexer;
    }
}