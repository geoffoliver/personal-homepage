<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\FeedParser;

use Cake\Http\Client;
use Cake\Cache\Cache;

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

    public function feed($id)
    {
        if (!$id) {
            throw new \Exception('Missing friend ID');
        }

        $fp = new FeedParser();

        $friend = $this->Friends->find()
            ->where([
                'Friends.id' => $id
            ])
            ->first();

        if (!$friend) {
            throw new \Exception('Invalid friend ID');
        }

        /*
        $cached = Cache::read($friend->id, 'feeds');
        if ($cached) {
            return $this->response
                ->withType('text/xml')
                ->withHeader('X-Cached-Result', 'true')
                ->withStringBody($cached);
        }
        */

        $client = new Client();

        $response = $client->get($friend->feed_url);

        dd($fp->normalize($response->getStringBody()));

        if ($body = $response->getStringBody()) {
            Cache::write($friend->id, $body, 'feeds');
        }

        return $this->response
            ->withType('text/xml')
            ->withBody($response->getBody());
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

    private function getDataFromSite($url)
    {
        // fire up an HTTP client
        $client = new Client();

        // request the URL
        $pageData = $client->get($url . '/site-info.json', ['redirect' => 10]);

        // response failed... oh well. bye!
        if (!$pageData || $pageData->isOk()) {
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
        $feedTags = $domXpath->query('/html/head/link[@rel="alternate"]');

        for ($i = 0; $i < $feedTags->length; $i++) {
            $type = $feedTags->item($i)->attributes->getNamedItem('type');
            if (!$type || strpos($type->value, '+xml') === false) {
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
        $appleIcons = $domXpath->query('/html/head/link[@rel="apple-touch-icon"]');
        $favicons = $domXpath->query('/html/head/link[@rel="shortcut icon"]');
        $moreFavicons = $domXpath->query('/html/head/link[@rel="icon"]');

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
                $icons[]= $iIcon->value;
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
                $icons[]= $iIcon->value;
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
