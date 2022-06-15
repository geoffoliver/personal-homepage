<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\Cache\Cache;
use Cake\Http\Client;
use Cake\Utility\Hash;

class FriendsController extends AppController
{
    public $paginate = [
        'Friends' => [
            'order' => [
                'name' => 'ASC',
                'created' => 'DESC'
            ]
        ]
    ];

    public function initialize(): void
    {
        parent::initialize();

        // allow unauthed users to view friend list and friend icons
        $this->Authentication->allowUnauthenticated(['index', 'icon']);
    }

    public function index()
    {
        // get all of our friends
        $friends = $this->Friends->find()
            ->order([
                'Friends.name' => 'ASC',
                'Friends.created' => 'ASC'
            ])
            ->all();

        // done
        $this->set([
            'friends' => $friends,
            'user' => $this->request->getAttribute('identity')
        ]);
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
                Cache::delete($id, 'icons');
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

    public function fetchDetails()
    {
        $this->request->allowMethod(['post']);

        // make sure we've got a URL to lookup
        $url = $this->request->getData('url');
        if (!$url) {
            throw new \Exception('Missing URL');
        }

        // first, see if the site we're trying to lookup is on the same platform
        $results = $this->getDataFromSite($url);

        // site isn't on the same platform, so just try to get as much data
        // about the site as possible from the HTML of the URL provided
        if (!$results) {
            $results = $this->getDataFromHtml($url);
        }

        $this->set(array_merge(
            $results,
            [
                '_serialize' => [
                    'feeds',
                    'description',
                    'name',
                    'icons',
                    'samePlatform'
                ]
            ]
        ));
    }

    public function icon($id, $update = false)
    {
        $friend = $this->Friends->get($id);

        // make sure we're trying to see an icon for a friend that exists
        if (!$friend) {
            throw new \Exception('Invalid friend');
        }

        $icon = Cache::read($friend->id, 'icons');

        // make sure the friend has an icon
        if ($friend->icon) {
            // if we're not asking to update, check if we _should_ update because
            // the icon is too old
            if (!$update) {
                $update = !$icon;
            }

            // should we update the icon?
            if ($update) {
                // try it out!
                if ($newIcon = @file_get_contents($friend->icon)) {
                    $icon = $newIcon;
                    Cache::write($friend->id, $icon, 'icons');
                }
            }

            if ($icon) {
                // try to figure out the type we should send
                $basename = basename($friend->icon);
                $parts = explode('?', $basename);

                $iconName = $parts[0];

                // we can only do this if there's a dot, presumably followed by the extension
                if (strpos($iconName, '.') !== false) {
                    $nameParts = explode('.', $iconName);
                    $ext = array_pop($nameParts);

                    if ($ext) {
                        // yay, things are good!
                        $response = $this->response->withType($ext);
                    }
                }

                // try to return the icon for the friend
                return $this->response->withStringBody($icon);
            }
        }

        // if we're still here, just display a random icon for the friend
        $icon = new \Jdenticon\Identicon();
        $icon->setValue($friend->id);
        $icon->setSize(64);
        $icon->displayImage('png');
        exit();
    }

    private function getDataFromSite($url)
    {
        // fire up an HTTP client
        $client = new Client();

        // request the URL
        $pageData = $client->get($url . '/site-info.json', ['redirect' => 10]);

        // response failed... oh well. bye!
        if (!$pageData || !$pageData->isOk()) {
            return false;
        }

        $body = $pageData->getStringBody();

        // nothing in the body? bye!
        if (!$body) {
            return false;
        }

        // convert the response into some nice json
        $data = json_decode($body, true);

        // no data to work with? bye!!
        if (!$data) {
            return false;
        }

        // pull some bits out of the data
        $feeds = [];
        if ($feed = Hash::get($data, 'feed')) {
            $feeds = [$feed];
        }

        $icons = [];
        if ($icon = Hash::get($data, 'icon')) {
            $icons = [$icon];
        }

        return [
            'name' => Hash::get($data, 'name'),
            'description' => Hash::get($data, 'description'),
            'feeds' => $feeds,
            'icons' => $icons,
            'samePlatform' => true
        ];
    }

    private function getDataFromHtml($url)
    {
        // fire up an HTTP client
        $client = new Client();

        // request the URL
        $pageData = $client->get($url, ['redirect' => 10]);

        // no response? oh well, bye!
        if (!$pageData) {
            throw new \Exception(__('Server received invalid response'));
        }

        // get the HTML for the page
        $body = $pageData->getStringBody();

        // no HTML returned, bail out
        if (!$body) {
            throw new \Exception(__('Server received empty response'));
        }

        // we don't care about all your whiny invalid tag errors
        libxml_use_internal_errors(true);
        $domPage = new \DOMDocument('1.0', 'UTF-8');

        // load the HTML up so we can xpath it to find stuff
        $domPage->loadHtml($body);
        $domXpath = new \DOMXpath($domPage);

        // try to find RSS feeds
        $feeds = [];
        $feedTags = $domXpath->query('/html/*/link[@rel="alternate"]');

        for ($i = 0; $i < $feedTags->length; $i++) {
            $type = $feedTags->item($i)->attributes->getNamedItem('type');
            if (!$type) {//} || strpos($type->value, '+xml') === false) {
                continue;
            }

            if ($feedUrl = $feedTags->item($i)->attributes->getNamedItem('href')->value) {
                if (strpos($feedUrl, 'http') === false && strpos($feedUrl, $url) === false) {
                    $feedUrl = rtrim($url, '/') . '/' . ltrim($feedUrl, '/');
                }
                $feeds[]= $feedUrl;
            }
        }

        // try to find a name
        $titleField = $domXpath->query('/html/head/title');
        if ($nValue = $titleField->item(0)) {
            $name = $nValue->nodeValue;
        }

        // try to find an icon
        $icons = [];
        // prefer apple icons because they should always be PNGs
        $appleIcons = $domXpath->query('/html/*/link[@rel="apple-touch-icon"]');
        $favicons = $domXpath->query('/html/*/link[@rel="shortcut icon"]');
        $moreFavicons = $domXpath->query('/html/*/link[@rel="icon"]');

        for ($i = 0; $i < $appleIcons->length; $i++) {
            if ($iIcon = $appleIcons->item($i)->attributes->getNamedItem('href')) {
                $icon = $iIcon->value;
                if (!$icon) {
                    continue;
                }

                if (strpos($icon, 'http') === false && strpos($icon, $url) === false) {
                    $icon = rtrim($url, '/') . '/' . ltrim($icon, '/');
                }

                $icons[]= $icon;
            }
        }

        for ($i = 0; $i < $favicons->length; $i++) {
            if ($iIcon = $favicons->item($i)->attributes->getNamedItem('href')) {
                $icon = $iIcon->value;
                if (!$icon) {
                    continue;
                }
                if (strpos($icon, 'http') === false && strpos($icon, $url) === false) {
                    $icon = rtrim($url, '/') . '/' . ltrim($icon, '/');
                }
                $icons[]= $icon;
            }
        }

        for ($i = 0; $i < $moreFavicons->length; $i++) {
            if ($iIcon = $moreFavicons->item($i)->attributes->getNamedItem('href')) {
                $icon = $iIcon->value;
                if (!$icon) {
                    continue;
                }

                if (strpos($icon, 'http') === false && strpos($icon, $url) === false) {
                    $icon = rtrim($url, '/') . '/' . ltrim($icon, '/');
                }
                $icons[]= $icon;
            }
        }

        // try to find a description
        $description = null;
        // first, let's look for a meta description
        $descField = $domXpath->query('/html/head/meta[@name="description"]');
        if ($descField->length === 0) {
            // ok, look for an og:description tag
            $descField = $domXpath->query('/html/head/meta[@property="og:description"]');
        }

        if ($descField->length > 0) {
            if ($dContent = $descField->item(0)->attributes->getNamedItem('content')) {
                $description = $dContent->value;
            }
        }

        return [
            'feeds' => $feeds,
            'description' => $description,
            'name' => $name,
            'icons' => $icons,
            'samePlatform' => false
        ];
    }
}
