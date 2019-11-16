<?php

namespace App\Lib;
use Cake\Cache\Cache;

use Embed\Embed;

class oEmbed {

    private static $instance = null;

    private $embeddable = [
        '/https:\/\/twitter\.com\/[^\/]+\/status\/[\d]+/',
        '/https:\/\/youtube\.com\/watch\?v=.+/',
        '/https:\/\/open\.spotify\.com\/.+/',
        '/https:\/\/\.flickr\.com\/photos\/.+/',
        '/https:\/\/www\.flickr\.com\/photos\/.+/',
        '/https:\/\/t\.co\/.+/',
        '/https:\/\/www\.reddit\.com\/.+/'
    ];

    private $cacheName = 'oEmbed';

    private function __construct()
    {
        // nothing happens here
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new oEmbed();
        }

        return self::$instance;
    }

    public function embed($content)
    {
        // pull out any links from the content
        if (preg_match_all('/<a[^>]+href=[^>]+>[^<]+<\/a>/', $content, $links)) {
            // loop over the links so we can _maybe_ embed it
            foreach ($links[0] as $link) {
                // this is an easy way to get the href from a link... right?
                $xLink = new \SimpleXMLElement($link);
                $url = isset($xLink['href']) ? (string)$xLink['href'] : false;

                if (!$url) {
                    continue;
                }

                // get the embed info fresh
                $embedInfo =$this->getEmbedInfo($url);

                if (!$embedInfo) {
                    continue;
                }

                if ($embedInfo->url) {
                    // replace any stupid short URLs with full URLs
                    $content = str_replace($url, $embedInfo->url, $content);
                }

                if ($embedInfo->code) {
                    // tack on the embedded thing
                    $content .= $this->wrapEmbed($embedInfo->code);
                }
            }
        }

        return $content;
    }

    public function getEmbedInfo($url)
    {
        $cacheKey = md5($url);
        $cached = Cache::read($cacheKey, $this->cacheName);

        if ($cached !== false) {
            // we've already worked with this URL, hand back the info we have
            return $cached;
        }

        $result = null;

        foreach ($this->embeddable as $emb) {
            if (preg_match($emb, $url)) {
                try {
                    $result = Embed::create($url);
                } catch (\Exception $ex) {
                    // we don't really care if this fails, but we need to catch
                    // any exceptions that might be thrown
                }

                // save _whatever_ we got back into the cache
                Cache::write($cacheKey, $result, $this->cacheName);
                return $result;
            }
        }

        // nothing matched, whatever, we don't care. cache that and move on
        Cache::write($cacheKey, null, $this->cacheName);
        return null;
    }

    public function wrapEmbed($code) {
        return '<div class="pf-oembed">' . $code . '</div>';
    }
}
