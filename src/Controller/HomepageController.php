<?php
namespace App\Controller;

use App\Controller\AppController;

class HomepageController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $homepage = $this->paginate($this->Homepage);

        $this->set(compact('homepage'));
    }
}
