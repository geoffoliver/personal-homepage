<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Post Entity
 *
 * @property string $id
 * @property string $url_alias
 * @property string $name
 * @property string $content
 * @property bool $public
 * @property string $user_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Media[] $media
 */
class Post extends Entity
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
        'name' => true,
        'content' => true,
        'source' => true,
        'import_source' => true,
        'embeds' => true,
        'is_link' => true,
        'public' => true,
        'allow_comments' => true,
        'user_id' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'medias' => true,
        'comments' => true
    ];
}
