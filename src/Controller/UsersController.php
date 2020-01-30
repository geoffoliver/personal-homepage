<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated([
            'login',
            'resetPassword'
        ]);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    /*
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }
    */

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    /*
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Medias', 'Posts'],
        ]);

        $this->set('user', $user);
    }
    */

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    /*
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }
    */

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    /*
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }
    */

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    /*
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    */

    public function login()
    {
        $result = $this->Authentication->getResult();
        $session = $this->getRequest()->getSession();
        $attempts = $session->read('loginAttempts', 0);
        $lastAttempt = $session->read('lastAttempt');

        if (
            $lastAttempt &&
            $lastAttempt >= strtotime('-5 minutes') &&
            $attempts >= 5
        ) {
            // user tried to login too many times and failed
            $this->Flash->error(__('Too many failed login attempts.'));
            return $this->redirect('/');
        }

        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            $session->delete('loginAttempts');
            $authService = $this->Authentication->getAuthenticationService();
            $redirect = $this->request->getQuery(
                'redirect',
                ['controller' => 'Homepage', 'action' => 'feed']
            );
            return $this->redirect($redirect);
        }

        if ($this->request->is(['post']) && !$result->isValid()) {
            $session->write('loginAttempts', $attempts + 1);
            $session->write('lastAttempt', time());
            $this->Flash->error(__('Invalid username or password'));
        }
    }

    public function logout()
    {
        $this->request->allowMethod(['post']);

        $this->Authentication->logout();
        $this->Flash->success(__('You have been logged out'));
        return $this->redirect([
            'controller' => 'Homepage',
            'action' => 'index'
        ]);
    }

    public function resetPassword($hash = null)
    {
        if (!$hash) {
            throw new \Exception(__('Invalid request'));
        }

        $user = $this->Users->find()
            ->where([
                'Users.reset_hash' => $hash
            ])
            ->first();

        if (!$user) {
            $this->Flash->error(__('Invalid request'));
            return $this->redirect([
                'controller' => 'Users',
                'action' => 'login'
            ]);
        }

        if ($this->request->is('post')) {
            $password = $this->request->getData('password');
            if (!$password) {
                throw new \Exception('Missing password');
            }

            $user->password = $password;
            $user->reset_hash = null;
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Password reset successfully'));
                return $this->redirect([
                    'controller' => 'Users',
                    'action' => 'login'
                ]);
            } else {
                $this->Flash->error(__('Unable to reset password'));
            }
        }

        $this->set([
            'user' => $user
        ]);
    }
}
