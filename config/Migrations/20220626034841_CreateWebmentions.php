<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateWebmentions extends AbstractMigration
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
        $table = $this->table('webmentions');
        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('source', 'string', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('target', 'string', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('status', 'enum', [
            'default' => 'pending',
            'values' => ['pending', 'created', 'updated', 'duplicate', 'invalid'],
        ]);
        $table->addColumn('type', 'string', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('type_id', 'uuid', [
            'default' => null,
            'null' => false,
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
        $table->create();

        $table = $this->table('comments');
        $table->addColumn('type', 'enum', [
            'default' => 'comment',
            'after' => 'model_id',
            'values' => ['comment', 'mention', 'bookmark', 'like', 'repost', 'reply'],
        ]);
        $table->update();

    }
}
