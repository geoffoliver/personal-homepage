<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Albums Controller
 *
 * @property \App\Model\Table\AlbumsTable $Albums
 *
 * @method \App\Model\Entity\Album[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AlbumsController extends AppController
{
    public $types = [];

    public function initialize()
    {
        parent::initialize();

        // types of albums we can display
        $this->types = [
            'photos' => __('Photo'),
            'videos' => __('Video')
        ];

        // unauthed users can view indexes and details for albums
        $this->Authentication->allowUnauthenticated([
            'index',
            'view',
        ]);
    }

    public function index($type = 'photos')
    {
        // make sure a valid type is being requested
        if (!array_key_exists($type, $this->types)) {
            // just send the user off to the photos page
            return $this->redirect(['_name' => 'listAlbums', 'photos']);
        }

        // try to find all the albums of the type being requested
        $albums = $this->Albums->find()
            ->where([
                'Albums.type' => $type
            ])
            ->contain([
                'CoverMedias',
                'Medias' => [
                    'sort' => [
                        'Medias.created' => 'DESC'
                    ]
                ]
            ])
            ->order([
                'Albums.created' => 'DESC'
            ])
            ->all();

        // easy peasy
        $this->set([
            'title' => $this->types[$type],
            'type' => $type,
            'albums' => $albums
        ]);
    }
    /**
     * View method
     *
     * @param string|null $id Album id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // get the album along with all it's media and comments
        $album = $this->Albums->get($id, [
            'contain' => [
                'CoverMedias',
                'Medias' => [
                    'sort' => [
                        'Medias.created' => 'DESC'
                    ]
                ],
                'Comments' => [
                    'sort' => [
                        'Comments.created' => 'DESC'
                    ]
                ]
            ],
        ]);
        // done
        $this->set('album', $album);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    /*
    public function add()
    {
        $album = $this->Albums->newEntity();
        if ($this->request->is('post')) {
            $album = $this->Albums->patchEntity($album, $this->request->getData());
            if ($this->Albums->save($album)) {
                $this->Flash->success(__('The album has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The album could not be saved. Please, try again.'));
        }
        $this->set(compact('album'));
    }
    */

    /**
     * Edit method
     *
     * @param string|null $id Album id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    /*
    public function edit($id = null)
    {
        $album = $this->Albums->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $album = $this->Albums->patchEntity($album, $this->request->getData());
            if ($this->Albums->save($album)) {
                $this->Flash->success(__('The album has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The album could not be saved. Please, try again.'));
        }
        $this->set(compact('album'));
    }
    */

    /**
     * Delete method
     *
     * @param string|null $id Album id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    /*
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $album = $this->Albums->get($id);
        if ($this->Albums->delete($album)) {
            $this->Flash->success(__('The album has been deleted.'));
        } else {
            $this->Flash->error(__('The album could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    */
}
