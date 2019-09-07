<?php

namespace App\Controller;

use App\Controller\AppController;

class MediasController extends AppController
{
    public $paginate = [
        'Posts' => [
            'limit' => 10000,
            'conditions' => [
                'public' => true,
            ],
            'contain' => [
                'Medias',
            ],
        ],
    ];

    public function initialize()
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated([
            'view',
            'heroBackground',
            'profilePhoto'
        ]);
    }

    public function view($id)
    {
        $media = $this->Medias->find()
            ->where([
                'Medias.id' => $id
            ])
            ->first();

        if (!$media) {
            $this->Flash->error(__('Invalid media item.'));
            return $this->redirect('/');
        }

        dd($media);
    }

    public function upload()
    {
        $this->request->allowMethod(['post']);

        $success = false;
        $media = null;

        $user = $this->request->getAttribute('identity');

        $file = $this->request->getData('file');

        if (!$file) {
            throw new \Exception(__('Missing upload'));
        }

        $data = [
            'name' => $this->request->getData('name'),
            'description' => $this->request->getData('description'),
            'user_id' => $user->id,
        ];

        if ($created = $this->Medias->uploadAndCreate($file, false, $data)) {
            $media = $created;
            $success = true;
        }

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

    public function heroBackground()
    {
        $this->loadModel('Settings');
        $hero = $this->Settings->find()->where(['Settings.name' => 'site.hero-background'])->first();
        $file = WWW_ROOT . 'img' . DS . 'default-hero-background.jpg';

        if ($hero) {
            $media = $this->Medias->find()->where(['Medias.id' => $hero->value])->first();

            if ($media) {
                $mFile = WWW_ROOT . 'media' . DS . $media->value;
                if (file_exists($mFile) && is_readable($mFile)) {
                    $file = $mFile;
                }
            }
        }

        return $this->response->withFile($file);
    }

    public function profilePhoto()
    {
        $this->loadModel('Settings');
        $profile = $this->Settings->find()->where(['Settings.name' => 'site.profile-photo'])->first();
        $file = WWW_ROOT . 'img' . DS . 'default-profile-photo.jpg';

        if ($profile) {
            $media = $this->Medias->find()->where(['Medias.id' => $profile->value])->first();

            if ($media) {
                $mFile = WWW_ROOT . 'media' . DS . $media->value;
                if (file_exists($mFile) && is_readable($mFile)) {
                    $file = $mFile;
                }
            }
        }

        return $this->response->withFile($file);
    }
}
