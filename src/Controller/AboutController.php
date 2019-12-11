<?php

namespace App\Controller;

use App\Controller\AppController;

class AboutController extends AppController
{
    public $use = ['Settings'];

    public function index()
    {
        $aboutIntro = $this->Settings->find()
            ->where([
                'Settings.name' => 'homepage-about'
            ])
            ->first();

        $aboutPage = $this->Settings->find()
            ->where([
                'Settings.name' => 'about-page'
            ])
            ->first();

        $this->set([
            'aboutIntro' => $aboutIntro,
            'aboutPage' => $aboutPage
        ]);
    }
}
