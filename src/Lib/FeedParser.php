<?php

namespace App\Lib;

use Cake\Http\Client;

class FeedParser
{
    public function fetch($feedUrl)
    {
        if (!$feedUrl) {
            throw new \Exception('Missing Feed URL');
        }

        // fire up an HTTP client
        $client = new Client();

        // try to get a response for the feed URL
        $response = $client->get($feedUrl);

        if (!$response) {
            throw new \Exception(__('Invalid response'));
        }

        // get the XML for the feed
        $feedXml = $response->getStringBody();

        // no XML to parse? later
        if (!$feedXml) {
            return null;
        }

        if ($feed = $this->jsonify($feedXml)) {
            $feed['feed_url'] = $feedUrl;
        }

        return json_encode($feed);
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

        return [
            'version' => 'https://jsonfeed.org/version/1',
            'title' => $title,
            'home_page_url' => $homepageUrl,
            'date_modified' => $dateModified,
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

        return [
            'id' => $id,
            'title' => $title,
            'url' => $url,
            'external_url' => $source,
            'date_published' => $pubDate,
            'date_modified' => $modDate,
            'summary' => $summary,
            'content_html' => $contentHtml,
            'content_text' => $contentText,
            'attachments' => $attachments,
            'tags' => $categories,
            'author' => $author,
            '_page_feed' => [
                'about' => 'Custom fields for PageFeed',
                'comments' => [
                    'url' => $commentsUrl,
                    'total' => (string) $totalComments,
                ]
            ],
        ];
    }

}
