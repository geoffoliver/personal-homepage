<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FeedItem Entity
 *
 * @property string $id
 * @property string $following_id
 * @property string $title
 * @property string|null $summary
 * @property string $url
 * @property array|null $author
 * @property string|null $content
 * @property string|null $media
 * @property bool $is_read
 * @property array|null $page_feed
 * @property \Cake\I18n\FrozenTime $date_published
 * @property \Cake\I18n\FrozenTime $date_modified
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Following $following
 */
class FeedItem extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'following_id' => true,
        'title' => true,
        'summary' => true,
        'url' => true,
        'author' => true,
        'content' => true,
        'media' => true,
        'is_read' => true,
        'page_feed' => true,
        'date_published' => true,
        'date_modified' => true,
        'created' => true,
        'modified' => true,
        'following' => true,
    ];
}
