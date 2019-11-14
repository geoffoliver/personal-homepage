<?php

namespace App\Lib;

use Embed\Embed;

class oEmbed {

    private $embeddable = [
        '/https:\/\/twitter\.com\/[^\/]+\/status\/[\d]+/',
        '/https:\/\/youtube\.com\/watch\?v=.+/',
        '/https:\/\/open\.spotify\.com\/.+/',
        '/https:\/\/\.flickr\.com\/photos\/.+/',
        '/https:\/\/www\.flickr\.com\/photos\/.+/',
        '/https:\/\/t.co\/.+/'
    ];


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

                // if we get some embed info back...
                if ($embedInfo =$this->getEmbedInfo($url)) {
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
        }

        return $content;
    }

    public function getEmbedInfo($url)
    {
        foreach ($this->embeddable as $emb) {
            if (preg_match($emb, $url)) {
                try {
                    return Embed::create($url);
                } catch (\Exception $ex) {
                    // we don't really care if this fails, but we need to catch
                    // any exceptions that might be thrown
                }
                return null;
            }
        }

        return null;
    }

    public function wrapEmbed($code) {
        return '<div class="pf-oembed">' . $code . '</div>';
    }
}
