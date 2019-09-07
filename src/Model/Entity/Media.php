<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Media Entity
 *
 * @property string $id
 * @property string $post_id
 * @property string $mime
 * @property string $thumbnail
 * @property string $local_filename
 * @property string $original_filename
 * @property string $user_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Post $post
 * @property \App\Model\Entity\User $user
 */
class Media extends Entity
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
        'post_id' => true,
        'album_id' => true,
        'mime' => true,
        'name' => true,
        'description' => true,
        'size' => true,
        'thumbnail' => true,
        'square_thumbnail' => true,
        'local_filename' => true,
        'original_filename' => true,
        'import_source' => true,
        'user_id' => true,
        'created' => true,
        'modified' => true,
        'post' => true,
        'user' => true,
        'comments' => true
    ];
}
