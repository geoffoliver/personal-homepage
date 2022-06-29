<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

use App\Lib\FeedParser;
use App\Model\Table\FeedItemsTable;

/**
 * Friend Entity
 *
 * @property string $id
 * @property string $url
 * @property string $feed_url
 * @property string $name
 * @property string|null $description
 * @property string|null $icon
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */

class Friend extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'url' => true,
        'feed_url' => true,
        'name' => true,
        'description' => true,
        'icon' => true,
        'created' => true,
        'modified' => true
    ];

    public function getFeed($encode = true)
    {
        // fire up a feeed parser
        $fp = new FeedParser();

        // try to fetch the feed
        return $fp->fetch($this->feed_url, $this->url, $encode);
    }

    public function syncFeed()
    {
        // get the feed for the friend
        $feed = $this->getFeed(false);
        $feedItems = new FeedItemsTable();

        // if we've got a feed and it looks good...
        if ($feed && isset($feed->items) && $feed->items) {
            foreach ($feed->items as $item) {
                $existing = $feedItems->find()
                    ->where([
                        'url' => $item->url,
                    ])
                    ->first();

                // if we already know about this, no need to re-add it... maybe.
                if ($existing) {
                    continue;
                }

                $feedItem = $feedItems->newEntity([
                    'friend_id' => $this->id,
                    'title' => $item->title,
                    'summary' => isset($item->summary) ? $item->summary : null,
                    'url' => $item->url,
                    'author' => isset($item->author) ? $item->author : null,
                    'date_published' => $item->date_published,
                    'date_modified' => isset($item->date_modified) ? $item->date_modified : null,
                    'content' => isset($item->content_html) ? $item->content_html : null,
                    'media' => isset($item->media) ? $item->media : null,
                    'page_feed' => isset($item->_page_feed) ? $item->_page_feed : null,
                ]);

                $feedItems->save($feedItem);
            }
        }
    }
}
