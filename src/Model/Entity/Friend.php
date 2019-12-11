<?php
namespace App\Model\Entity;

use App\Lib\FeedParser;

use Cake\Cache\Cache;
use Cake\ORM\Entity;

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
        return $fp->fetch($this->feed_url, $encode);
    }
}
