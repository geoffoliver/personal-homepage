<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * FeedItems Controller
 *
 * @property \App\Model\Table\FeedItemsTable $FeedItems
 */
class FeedItemsController extends AppController
{
    public function markRead($id)
    {
        if (!$this->request->is('post')) {
            throw new \Cake\Http\Exception\BadRequestException('Bad Request');
        }

        $item = $this->FeedItems->findById($id)->first();

        if ($item) {
            $item->set('is_read', true);
            $this->FeedItems->save($item);
        }

        $this->set([
            'status' => 'ok',
            '_serialize' => [
                'status'
            ],
        ]);
    }
}
