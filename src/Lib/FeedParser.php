<?php

namespace App\Lib;

use Cake\Http\Client;
use Cake\I18n\Time;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class FeedParser
{
    private $purifier = null;

    public function fetch($feedUrl = null, $baseUrl = null, $encode = true)
    {
        // wtf are you even doing here?
        if (!$feedUrl) {
            throw new \Exception('Missing Feed URL');
        }

        // make an HTML purifier we'll use for cleaning up HTML content
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('AutoFormat.RemoveEmpty', true);
        if ($baseUrl) {
            $config->set('URI.Base', $baseUrl);
            $config->set('URI.MakeAbsolute', true);
        }
        $this->purifier = new \HTMLPurifier($config);

        // fire up an HTTP client
        $client = new Client();

        // we store last modified and etags so we can do partial gets and only
        // get stuff since our last request
        $cacheKey = md5($feedUrl);
        $lastModified = null;
        $eTag = null;
        $expires = null;
        $options = []; // this is where we'll stuff the last mod and etag values

        // check the cache to see if we've already got this feed
        $cached = Cache::read($cacheKey, 'feeds');

        // we found meta info about the feed in the cache, awesome
        if ($feedMeta = Cache::read($cacheKey, 'feed_meta')) {
            if ($expires = Hash::get($feedMeta, 'Expires')) {
                $expires = strtotime($expires[0]);
            }

            if ($lastModified = Hash::get($feedMeta, 'Last-Modified')) {
                $lastModified = $lastModified[0];
            }

            if ($eTag = Hash::get($feedMeta, 'ETag')) {
                $eTag = $eTag[0];
            }
        }

        // figure out if we'll do a conditional get or if we'll return cached data
        if ($lastModified && $eTag) {
            // if we've got a last modified and an etag, we can use them to do
            // a conditional get so we won't get any data that we already have
            $options = [
                'headers' => [
                    'If-Modified-Since' => $lastModified,
                    'If-None-Match' => $eTag
                ]
            ];
        } elseif ($cached && $expires && $expires > time()) {
            // the feed is already cached, the server doesn't support (no last mod
            // or etag), but it _does_ send back an 'Expires' header, and the
            // feed hasn't expired yet, so we can just hand back the cached feed.
            return $cached;
        }

        // try to get a response for the feed URL
        $response = $client->get($feedUrl, null, $options);

        // response failed, bail out
        if (!$response) {
            throw new \Exception(__('Invalid response'));
        }

        // cache meta info for the feed
        Cache::write($cacheKey, $response->getHeaders(), 'feed_meta');

        // get the XML for the feed
        $feedXml = $response->getStringBody();

        // no XML to parse? later
        if (!$feedXml) {
            // we have cached data, return that
            if ($cached) {
                return $cached;
            }
            // nothing we can do, bail out
            return null;
        }

        // turn the feed into JSON, because it's better that way :)
        if ($feed = $this->jsonify($feedXml)) {
            $feed->feed_url = $feedUrl;
        }

        if ($cached && !$feed) {
            // if the feed was in the cache, but we didn't get any results back,
            // use the cached feed.
            return $cached;
        }

        // there is cached data _and_ new data from the server, so we need to
        // merge the new data in
        if ($cached && $feed) {
            $items = array_merge($feed->items, $cached->items);
            $feed->items = [];
            $itemUrls = [];

            // since some servers (codinghorror on feedburner) don't provide last
            // modified _and_ etags, so we can't do conditional gets, and their
            // 'Expires' might be very short lived, which means we could end up
            // with duplicate data... so we need to fix that
            foreach ($items as $item) {
                if (!in_array($item->url, $itemUrls)) {
                    $itemUrls[]= $item->url;
                    $feed->items[]= $item;
                }
            }
        }

        // cache the feed
        Cache::write($cacheKey, $feed, 'feeds');

        // sometimes we want a string back, what's the big deal?
        if ($encode) {
            return json_encode($feed);
        }

        // byeeee!
        return $feed;
    }

    public function jsonify($feedXml)
    {
        // try to parse the XML
        $xml = simplexml_load_string($feedXml);
        if ($xml === false) {
            return null;
        }

        $author = null;

        $title = null;
        $homepageUrl = null;
        $dateModified = null;
        $author = null;
        $description = null;

        $posts = [];

        $root = $xml;
        $isAtom = false;

        if ($xml->channel) {
            $root = $xml->channel;

            if ($itm = $root->item) {
                $items = $itm;
            }

            if ($desc = $root->description) {
                $description = (string) $desc;
            }

            if ($url = $root->link) {
                $homepageUrl = (string) $url;
            }

            if ($modDate = $root->lastBuildDate) {
                $dateModified = (string) $modDate;
            }
        } else {
            $isAtom = true;

            if ($itm = $root->entry) {
                $items = $root->entry;
            }

            if ($desc = $root->subtitle) {
                $description = (string) $desc;
            }

            if ($url = $root->id) {
                $homepageUrl = (string) $url;
            }

            if ($modDate = $root->updated) {
                $dateModified = (string) $modDate;
            }
        }

        if ($t = $root->title) {
            $title = (string) $t;
        }

        if ($chanAuth = $root->author) {
            if ($authName = $chanAuth->name) {
                $author = [
                    'name' => (string) $authName
                ];
            }
        }

        return (object)[
            'version' => 'https://jsonfeed.org/version/1',
            'title' => $title,
            'home_page_url' => $homepageUrl,
            'date_modified' => new Time($dateModified),
            'author' => $author,
            'description' => $description,
            'next_url' => null,
            'items' => $this->parseItems(
                $isAtom,
                $xml->getDocNamespaces(),
                $items
            ),
        ];

    }

    public function parseItems($isAtom, $namespaces, $items)
    {
        $parsed = [];

        // parse out items from the feed
        if ($items) {
            foreach ($items as $item) {
                $parsed[]= $this->parseItem($isAtom, $namespaces, $item);
            }
        }

        return $parsed;
    }

    public function parseItem($isAtom, $namespaces, $item)
    {
        // figure out how many comments the post has
        $totalComments = 0;

        if (array_key_exists('slash', $namespaces)) {
            $slashComments = $item->xpath("slash:comments");
            if ($slashComments) {
                $totalComments = (string) $slashComments[0];
            }
        }

        // figure out if there are any categories
        $categories = [];
        if ($cats = $item->xpath('category')) {
            foreach ($cats as $cat) {
                $categories[] = (string) $cat;
            }
        }

        // figure out if there's an author
        $author = null;
        if ($itemAuthor = $item->author) {
            if ($authName = $itemAuthor->name) {
                $author = [
                    'name' => (string) $authName
                ];
            }
        }

        if (!$author && array_key_exists('dc', $namespaces)) {
            $dcCreator = $item->xpath("dc:creator");
            if ($dcCreator) {
                $author = [
                    'name' => (string)$dcCreator[0]
                ];
            }
        }

        // figure out if there's some media
        $media = null;
        if (array_key_exists('media', $namespaces)) {
            $medias = $item->xpath('media:content');
            if (
                $medias &&
                isset($medias[0]->attributes()->url) &&
                $medias[0]->attributes()->url
            ) {
                $media = (string)$medias[0]->attributes()->url;
            }
        }

        if (!$media) {
            $enclosure = $item->xpath('enclosure');
            if (
                $enclosure &&
                isset($enclosure[0]->attributes()->url) &&
                $enclosure[0]->attributes()->url
            ) {
                $media = (string)$enclosure[0]->attributes()->url;
            }
        }

        $url = null;
        $source = null;
        $commentsUrl = null;
        $pubDate = null;
        $contentHtml = null;
        $attachments = null;

        if ($isAtom) {
            $id = (string) $item->id;
            $title = (string) $item->title;
            if ($link = $item->link) {
                foreach ($link->attributes() as $k => $v) {
                    if ($k === 'href') {
                        $url = (string)$v;
                        break;
                    }
                }
            }
            if ($pDate = $item->published) {
                $pubDate = (string) $pDate;
            }
            $modDate = (string) $item->updated;

            if (!$pubDate && $modDate) {
                $pubDate = $modDate;
            }

            $contentHtml = (string) $item->content;
            $summary = (string) $item->summary;

            if (!$summary) {
                $summary = strip_tags($contentHtml);
            }
        } else {
            $id = (string) $item->guid;
            $title = (string) $item->title;
            $url = (string) $item->link;
            $source = (string) $item->source;
            $pubDate = (string) $item->pubDate;
            $modDate = $pubDate;
            $summary = (string)$item->description;

            if (array_key_exists('content', $namespaces)) {
                if ($encodedContent = $item->xpath('content:encoded')) {
                    $contentHtml = $encodedContent[0];
                }
            }

            if (!$contentHtml) {
                if ($item->content) {
                    $contentHtml = (string) $item->content;
                }
            }

            if (!$contentHtml && $summary) {
                $contentHtml = $summary;
            }

            if ($enclosure = $item->enclosure) {
                $attUrl = null;
                $attSize = null;
                $attType = null;

                foreach ($item->enclosure->attributes() as $k => $v) {
                    switch ($k) {
                        case 'url':
                            $attUrl = (string)$v;
                            break;
                        case 'length':
                            $attSize = (string)$v;
                            break;
                        case 'type':
                            $attType = (string)$v;
                            break;
                    }
                }

                if ($attUrl && $attSize && $attType) {
                    $attachments = [
                        [
                            'url' => $attUrl,
                            'size_in_bytes' => $attSize,
                            'mime_type' => $attType
                        ]
                    ];
                }
            }

            $commentsUrl = (string) $item->comments;
        }

        $readMore = [
            'read more...',
            '...read more',
            '…read more',
            'read more…',
            'read the rest',
            'read the rest...',
            'read the rest…',
            'read the rest&#8230;',
            '&#8230;read more',
            'read more&#8230;',
            '&hellip;read more',
            'read more&hellip;',
            '[…]',
            '[&#8230;]',
            '[&hellip;]'
        ];

        $contentText = strip_tags($contentHtml);
        $contentText = str_ireplace($readMore, '', $contentText);

        if ($summary) {
            $summary = strip_tags(str_ireplace($readMore, '', $summary));

            // replace UTF-8 spaces with regular ASCII spaces
            $summary = str_replace("\xc2\xa0", "\x20", $summary);

            $summary = trim($summary);

            $lastChar = mb_substr($summary, -1);

            $validLastChars = ['.', '?', '!', '...', '…'];
            if (!in_array($lastChar, $validLastChars)) {
                $summary.= '...';
            }
        }

        return (object)[
            'id' => $id,
            'title' => $title,
            'url' => $url,
            'external_url' => $source,
            'date_published' => new Time($pubDate),
            'date_modified' => new Time($modDate),
            'summary' => $summary,
            'content_html' => $this->purifier->purify($contentHtml),
            'content_text' => $contentText,
            'attachments' => $attachments,
            'tags' => $categories,
            'author' => $author,
            'media' => $media,
            '_page_feed' => (object)[
                'about' => 'Custom fields for PageFeed',
                'comments' => (object)[
                    'url' => $commentsUrl,
                    'total' => (string) $totalComments,
                ]
            ],
        ];
    }

}
