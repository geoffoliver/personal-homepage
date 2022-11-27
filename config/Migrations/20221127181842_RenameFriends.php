<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RenameFriends extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $friends = $this->table('friends');

        $friends->rename('followings');

        $friends->update();

        $feedItems = $this->table('feed_items');

        $feedItems->renameColumn('friend_id', 'following_id');

        $feedItems->update();
    }
}
