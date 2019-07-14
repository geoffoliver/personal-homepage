<?php
namespace App\Controller;

use App\Controller\AppController;

class FeedController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->modelClass = false;
    }

    public function index()
    {
        // nothing for now
    }

}
