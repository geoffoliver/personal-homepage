<?php
namespace App\Controller;

use App\Controller\AppController;

class FeedController extends AppController
{
    public function index()
    {
        $feed = $this->paginate($this->Feed);

        $this->set(compact('feed'));
    }

}
