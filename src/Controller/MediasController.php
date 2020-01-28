<?php

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Hash;

use App\Controller\AppController;

class MediasController extends AppController
{
    private $types = [];

    public function initialize()
    {
        parent::initialize();

        // let everybody access a few methods
        $this->Authentication->allowUnauthenticated([
            'index',
            'view',
            'download',
            'heroBackground',
            'profilePhoto',
        ]);

        // these are the types in the `index` function we're capable of displaying
        $this->types = [
            'photos' => [
                'where' => 'image/%',
                'title' => __('Photos')
            ],
            'videos' => [
                'where' => 'video/%',
                'title' => __('Videos')
            ]
        ];
    }

    /**
     * Displays a list of media items. Pretty simple.
     *
     * @param $type string The type of media you want to view. Must be one of the
     * options from $this->types.
     */
    public function index($type = false)
    {
        // assume this will fail
        $medias = [];

        // give the page a default title
        $title = __('Media');

        // make sure we're only handling something we know how to deal with
        if (array_key_exists($type, $this->types)) {
            // set a better title
            $title = $this->types[$type]['title'];

            // lookup some media
            $medias = $this->Medias->find()
                ->where([
                    'Medias.mime LIKE' => $this->types[$type]['where']
                ])
                ->order([
                    'Medias.created' => 'DESC'
                ]);

            // only logged in users can see private media
            if (!$this->Authentication->getIdentity()) {
                $medias = $medias->where([
                    'Medias.public' => true
                ]);
            }
        }

        // all done!
        $this->set([
            'medias' => $medias->all(),
            'type' => $type,
            'title' => $title
        ]);
    }

    /**
     * Display an individual media item.
     *
     * @param $id uuid The ID of the media item you want to display
     */
    public function view($id)
    {
        // try to find a media entry based on the ID
        $media = $this->Medias->find()
            ->where([
                'Medias.id' => $id
            ])
            ->contain([
                'Users',
                'Albums',
                'Posts',
                'Comments' => [
                    'sort' => [
                        'Comments.created' => 'DESC'
                    ],
                    'conditions' => [
                        'Comments.approved' => true
                    ]
                ]
            ]);

        // if we're not logged in, we can only see public media
        if (!$this->Authentication->getIdentity()) {
            $media = $media->where([
                'Medias.public' => true
            ]);
        }

        $media = $media->first();

        // can't find the media? oh well, bye!
        if (!$media) {
            $this->Flash->error(__('Invalid media item.'));
            return $this->redirect('/');
        }

        // still here? show the media item
        $this->set([
            'media' => $media
        ]);
    }

    /**
     * Downloads an individual media item to a client.
     *
     * @param $id uuid The ID of the media item you want to display
     * @param $type string The type of media you want. Options are:
     *                     - original
     *                     - thumbnail
     *                     - square_thumbnail
     */
    public function download($id, $type = 'original')
    {
        $start = microtime(true);

        // try to find a media entry based on the ID
        $media = $this->Medias->find()
            ->where([
                'Medias.id' => $id
            ]);
            // not sure why this was here... probably some copy/pasta leftovers.
            // i'll leave it for now until i'm sure it was just a dumb mistake
            // TODO: delete this eventually
            // ->contain([
            //     'Users',
            //     'Albums',
            //     'Posts',
            //     'Comments' => [
            //         'sort' => [
            //             'Comments.created' => 'DESC'
            //         ],
            //         'conditions' => [
            //             'Comments.approved' => true
            //         ]
            //     ]
            // ]);

        // if we're not logged in, we can only see public media
        if (!$this->Authentication->getIdentity()) {
            $media = $media->where([
                'Medias.public' => true
            ]);
        }

        $media = $media->first();

        // can't find the media? oh well, bye!
        if (!$media) {
            throw new NotFoundException(__('Invalid media item'));
        }

        // this is where the file lives
        $file = null;
        $original = $this->Medias->mediaPath . DS . $media->local_filename;

        if ($type !== 'original' && strpos($media->mime, 'audio/') === 0) {
            $file = WWW_ROOT . 'img' . DS . 'file-audio-regular.svg';
        } else {
            switch ($type) {
                case 'original':
                    $file = $original;
                    break;
                case 'thumbnail':
                    if ($media->thumbnail) {
                        $f = $this->Medias->mediaPath . DS . $media->thumbnail;
                        $file = file_exists($f) ? $f : null;
                    }
                    break;
                case 'square_thumbnail':
                    if ($media->square_thumbnail) {
                        $f = $this->Medias->mediaPath . DS . $media->square_thumbnail;
                        $file = file_exists($f) ? $f : null;
                    }
                    break;
                default:
                    throw new NotFoundException(__('Invalid media type'));
                    break;
            }
        }

        if (!$file && $original) {
            $file = $original;
        }

        if (!$file) {
            throw new NotFoundException(__('Invalid file'));
        }

        // a directory is being requested, wtf?
        if (is_dir($file)) {
            throw new NotFoundException(__('Invalid request'));
        }

        // make sure the file exists
        if (!file_exists($file)) {
            throw new NotFoundException(__('File not found'));
        }

        // make sure we can read the file
        if (!is_readable($file)) {
            throw new NotFoundException(__('Unable to read file'));
        }

        // let the browser cache this file
        $this->response = $this->response
            ->withCache(filemtime($file), '+1 year');

        // generate an etag for the browser... don't hash the file for this
        // because, right now, you can't replace files, but maybe someday?
        // so in case that happens, generate a hash of the modified timestamp
        $response = $this->response->withEtag(md5($media->modified->timestamp));

        // only send a new response if the file has changed (probably never)
        if ($response->checkNotModified($this->request)) {
            return $response;
        }

        // still here? ok, send a response with an etag
        $this->response = $response;


        $end = microtime(true);
        $runtime = $end - $start;

        // hand back a file response
        return $this->response->withFile($file);
    }

    public function edit($id)
    {
        $media = $this->Medias->find()
            ->where([
                'Medias.id' => $id
            ])
            ->contain([
                'Posts',
                'Albums'
            ])
            ->first();

        if (!$media) {
            $this->Flash->error(__('Invalid media item.'));
            return $this->redirect('/');
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $media = $this->Medias->patchEntity($media, [
                'name' => $this->request->getData('name'),
                'description' => $this->request->getData('description'),
                'public' => $this->request->getData('public'),
                'allow_comments' => $this->request->getData('allow_comments'),
            ]);

            if ($this->Medias->save($media)) {
                $this->Flash->success(__('The media has been saved.'));

                return $this->redirect(['_name' => 'viewMedia', $media->id]);
            }

            $this->Flash->error(__('The media could not be saved. Please, try again.'));
        }

        $this->set([
            'media' => $media
        ]);
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
        $post = $this->Medias->get($id);
        if ($this->Medias->delete($post)) {
            $this->Flash->success(__('The media has been deleted.'));
        } else {
            $this->Flash->error(__('The media could not be deleted. Please, try again.'));
        }

        return $this->redirect(['_name' => 'photos']);
    }

    /**
     * Upload a media item
     */
    public function upload()
    {
        // only allow posts to upload files
        $this->request->allowMethod(['post']);

        // always so pessimistic, this one
        $success = false;
        $media = null;

        // the user who is uploading the file
        $user = $this->request->getAttribute('identity');

        // the file being uploaded
        $file = $this->request->getData('file');

        // well, what the shit?
        if (!$file) {
            throw new \Exception(__('Missing upload'));
        }

        // make some data that the media table can handle and upload the file
        $data = [
            'name' => $this->request->getData('name'),
            'description' => $this->request->getData('description'),
            'user_id' => $user->id,
            'allow_comments' => $this->request->getData('allow_comments', true),
            'public' => $this->request->getData('public', true)
        ];

        // try to upload the file, anyway
        if ($created = $this->Medias->uploadAndCreate($file, false, $data)) {
            $media = $created;
            $success = true;
        }

        // all done, bye!
        $this->set([
            'success' => $success,
            'data' => [
                'media' => $media,
            ],
            '_serialize' => [
                'success',
                'data',
            ],
        ]);
    }

    /**
     * Responds with the hero background image for the public homepage and maybe
     * other stuff, who knows.
     */
    public function heroBackground()
    {
        // we need settings because that's where this data is stored
        $this->loadModel('Settings');

        // the default hero background
        $file = WWW_ROOT . 'img' . DS . 'default-hero-background.jpg';

        // try to find the hero background setting media
        if ($hero = $this->getSettingMedia('cover-photo')) {
            $file = $hero;
        }

        // we're all done here, send the file out
        return $this->response->withFile($file);
    }

    /**
     * Responds with the profile photo for the public homepage and other stuff.
     */
    public function profilePhoto()
    {
        // the default profile photo
        $file = WWW_ROOT . 'img' . DS . 'default-profile-photo.jpg';

        // try to find the profile photo background media
        if ($photo = $this->getSettingMedia('picture')) {
            $file = $photo;
        }

        return $this->response->withFile($file);
    }

    /**
     * Finds a media item from a settings entry.
     *
     * @param $settingName string The name of the setting you want to find a media
     * item for
     *
     * @return $file mixed On failure, null. On success, a (full path) filename
     */
    private function getSettingMedia($settingName = '')
    {
        $setting = Hash::get($this->settings, $settingName);

        // no setting? can't help you. later!
        if (!$setting) {
            return null;
        }

        // try to find the media for the setting
        $media = $this->Medias->find()
            ->where([
                'Medias.id' => $setting
            ])
            ->first();

        // no media? no dice. bail out.
        if (!$media) {
            return null;
        }

        // this is where the file actually lives
        $mFile = $this->Medias->mediaPath . DS . $media->local_filename;

        // check that the file exists and we can access it
        if (file_exists($mFile) && is_readable($mFile)) {
            // we're all good here
            return $mFile;
        }

        // if we've made it this far, that sucks.
        return false;
    }
}
