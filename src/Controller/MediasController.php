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
        $this->Authentication->allowUnauthenticated(['heroBackground', 'profilePhoto']);
    }

    public function upload()
    {

        $success = true;

        $this->set([
            'success' => $success,
            'data' => [
                'hello' => 'world',
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
