<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieCollection;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 *
 * @method \App\Model\Entity\Comment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommentsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['add']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $session = $this->getRequest()->getSession();

        // try to make sure someone's not spamming our comments. 2 minutes should
        // be enough time... right?
        $tooSoon = strtotime('-2 minutes');

        // how many comments can a single IP have in $tooSoon if we can't find
        // the last comment timestamp in the session?
        $tooMany = 5;

        // get the IP for the user
        $ip = $this->request->clientIp();

        // find comments from the same IP
        $recentComments = $this->Comments->find()
            ->where([
                'ip' => $ip,
                'created >=' => date('Y-m-d H:i:s', $tooSoon)
            ])
            ->count();

        // we found comments!
        if ($recentComments > 0 ) {
            // this _should_ be here, but it could easily be fooled by clearing
            // out cookies, soooo...
            $lastComment = $session->read('lc', time());

            if ($recentComments >= $tooMany) {
                // if you've got more than $tooMany comments, you can piss right off
                // because you probably managed to do this by some sneaky means.
                $this->Flash->error(__('You added several comments very recently. Please wait a few minutes and try again.'));
                return $this->redirect($this->referer());
            } elseif ($lastComment >= $tooSoon) {
                // if you posted a comment too recently, you just need to wait :-)
                $this->Flash->error(__('You added a comment very recently. Please wait a few minutes and try again.'));
                return $this->redirect($this->referer());
            }
        }

        if ($this->request->is('post')) {
            // make a new comment entity
            $comment = $this->Comments->newEntity([
                'display_name' => $this->request->getData('display_name'),
                'posted_by' => $this->request->getData('posted_by'),
                'comment' => $this->request->getData('comment'),
                'model_id' => $this->request->getData('model_id'),
                'ip' => $ip
            ]);

            // try to create/save the comment
            if ($this->Comments->save($comment)) {
                $session->write('lc', time());
                // set some cookies so the comment form can populate a couple
                // fields automatically, so users can be lazy
                $cookieExp = strtotime('+20 years');
                setcookie("comment_email", $comment->posted_by, $cookieExp, '/');
                setcookie("comment_name", $comment->display_name, $cookieExp, '/');
                $this->Flash->success(__('Your comment has been added. It will not appear until it has been approved.'));
            } else {
                $this->Flash->error(__('Your comment could not be added. Please, try again.'));
            }
        }

        return $this->redirect($this->referer());
    }

    public function approve($id = null)
    {
        $this->request->allowMethod(['post']);
        $comment = $this->Comments->get($id);
        if (!$comment) {
            $this->Flash->error(__('Invalid comment'));
        } else {
            $comment->approved = true;

            if ($this->Comments->save($comment)) {
                $this->Flash->success(__('The comment has been approved.'));
            } else {
                $this->Flash->error(__('The comment could not be approved. Please, try again.'));
            }
        }

        return $this->redirect($this->referer());
    }

    /**
     * Delete method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $comment = $this->Comments->get($id);
        if ($this->Comments->delete($comment)) {
            $this->Flash->success(__('The comment has been deleted.'));
        } else {
            $this->Flash->error(__('The comment could not be deleted. Please, try again.'));
        }

        return $this->redirect($this->referer());
    }
}
