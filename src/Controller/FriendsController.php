<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Client;

class FriendsController extends AppController
{
    public function index()
    {
        $this->set([
            'friends' => $this->paginate($this->Friends),
            'user' => $this->request->getAttribute('identity')
        ]);
    }

    public function view($id = null)
    {
        $friend = $this->Friends->get($id, [
            'contain' => []
        ]);

        $this->set('friend', $friend);
    }

    public function feed($id) {
        if (!$id) {
            throw new \Exception('Missing friend ID');
        }

        $friend = $this->Friends->find()
            ->where([
                'Friends.id' => $id
            ])
            ->first();

        if (!$friend) {
            throw new \Exception('Invalid friend ID');
        }

        $client = new Client();

        return $this->response
            ->withType('text/xml')
            ->withBody($client->get($friend->feed_url)->getBody());
    }

    public function add()
    {
        $friend = $this->Friends->newEntity();

        if ($this->request->is('post')) {
            $friend = $this->Friends->patchEntity($friend, $this->request->getData());
            if ($this->Friends->save($friend)) {
                $this->Flash->success(__('The friend has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            dd($friend->getErrors());
            $this->Flash->error(__('The friend could not be saved. Please, try again.'));
        }
        $this->set(compact('friend'));
    }

    public function edit($id = null)
    {
        $friend = $this->Friends->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $friend = $this->Friends->patchEntity($friend, $this->request->getData());
            if ($this->Friends->save($friend)) {
                $this->Flash->success(__('The friend has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The friend could not be saved. Please, try again.'));
        }
        $this->set(compact('friend'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $friend = $this->Friends->get($id);
        if ($this->Friends->delete($friend)) {
            $this->Flash->success(__('The friend has been deleted.'));
        } else {
            $this->Flash->error(__('The friend could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
