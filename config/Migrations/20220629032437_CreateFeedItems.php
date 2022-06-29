<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateFeedItems extends AbstractMigration
{
    public $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('feed_items');

        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('friend_id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('title', 'string', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('summary', 'text', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('url', 'text', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('author', 'json', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('content', 'text', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('media', 'string', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('is_read', 'boolean', [
            'default' => false,
            'null' => false,
        ]);

        $table->addColumn('page_feed', 'json', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('date_published', 'datetime', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('date_modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);

        $table->addPrimaryKey([
            'id',
        ]);

        $table->addIndex(['friend_id']);

        $table->addIndex(['is_read']);

        $table->create();
    }
}
