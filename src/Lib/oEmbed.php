<?php

namespace App\Lib;
use Cake\Cache\Cache;

use Embed\Embed;
use Embed\Http\CurlDispatcher;

class oEmbed {

    // private static $instance = null;

    private $embeddable = [
        '/https:\/\/twitter\.com\/[^\/]+\/status\/[\d]+/',
        '/https:\/\/youtube\.com\/watch\?v=.+/',
        '/https:\/\/open\.spotify\.com\/.+/',
        '/https:\/\/\.flickr\.com\/photos\/.+/',
        '/https:\/\/www\.flickr\.com\/photos\/.+/',
        '/https:\/\/t\.co\/.+/',
        '/https:\/\/www\.reddit\.com\/.+/'
    ];

    private $cacheName = 'o_embed';

    private $dispatcher;

    public function __construct()
    {
        // nothing happens here
        $this->dispatcher = new CurlDispatcher([
            CURLOPT_HTTPHEADER => [],
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_ENCODING => '',
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Embed PHP library',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
    }

    // public static function getInstance()
    // {
    //     if (self::$instance === null) {
    //         self::$instance = new oEmbed();
    //     }

    //     return self::$instance;
    // }

    public function getEmbeds(\ArrayObject $entity)
    {
        $embeds = [];
        // pull out any links from the content
        if (preg_match_all('/(https?:\/\/[^\s]+)/', $entity['content'], $links)) {
            // loop over the links so we can _maybe_ embed them
            foreach ($links[0] as $link) {
                // strip out any leading/trailing parens in case we're inside
                // a markdown link
                $link = ltrim(rtrim($link, ')'), '(');

                // get the embed info fresh
                $embedInfo =$this->getEmbedInfo($link);

                if (!$embedInfo) {
                    continue;
                }

                if ($embedInfo->code) {
                    $embeds[]= $embedInfo->code;
                }
            }
        }

        return $embeds;
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

        if ($cached) {
            // we've already worked with this URL, hand back the info we have
            return $cached;
        }

        $result = null;

        /*
        foreach ($this->embeddable as $emb) {
            if (preg_match($emb, $url)) {
        */
                try {
                    $result = Embed::create($url, null, $this->dispatcher);
                } catch (\Exception $ex) {
                    $result = false;
                    // we don't really care if this fails, but we need to catch
                    // any exceptions that might be thrown
                }

                // save _whatever_ we got back into the cache
                Cache::write($cacheKey, $result, $this->cacheName);
                return $result;
        /*
            }
        }

        // nothing matched, whatever, we don't care. cache that and move on
        Cache::write($cacheKey, null, $this->cacheName);
        return null;
        */
    }

    public function wrapEmbed($code) {
        return '<div class="pf-oembed">' . $code . '</div>';
    }
}
