<?php

namespace App\Lib;

class FeedParser
{
    public function normalize($feedXml)
    {
        // no XML to parse? later
        if (!$feedXml) {
            return null;
        }

        // try to parse the XML
        $xml = simplexml_load_string($feedXml);
        if ($xml === false) {
            return null;
        }

        // start to build the feed... we're gonna rename some fields to things
        // that make a bit more sense in the context of our app
        $feed = [
            'name' => (string)$xml->channel->title,
            'url' => (string)$xml->channel->link,
            'description' => (string)$xml->channel->description,
            'updated' => (string)$xml->channel->lastBuildDate,
            'language' => (string)$xml->channel->language,
            'generator' => (string)$xml->channel->generator,
            'site' => (string)$xml->channel->site,
            'posts' => [],
        ];

        // parse out items from the feed
        if ($xml->channel->item) {
            foreach ($xml->channel->item as $item) {

                // figure out how many comments the post has
                $totalComments = 0;
                $slashComments = $item->xpath("slash:comments");
                if ($slashComments) {
                    $totalComments = (string)$slashComments[0];
                }

                // figure out if there are any categories
                $categories = [];
                if ($cats = $item->xpath('category')) {
                    foreach ($cats as $cat) {
                        $categories[]= (string)$cat;
                    }
                }

                // tack this post onto the lists of posts
                $feed['posts'][]= [
                    'id' => (string)$item->guid,
                    'name' => (string)$item->title,
                    'url' => (string)$item->link,
                    'created' => (string)$item->pubDate,
                    'preview' => (string)$item->description,
                    'fullContent' => (string)$item->description,
                    'attachments' => [],
                    'categories' => $categories,
                    'comments' => [
                        'url' => (string)$item->comments,
                        'total' => (string)$totalComments
                    ]
                ];
            }
        }

        dd($feed);
    }

}
