<?php
use Migrations\AbstractMigration;

class CreateFriends extends AbstractMigration
{

    public $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('friends');
        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('url', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('feed_url', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('name', 'string', [
          'default' => null,
          'limit' => 255,
          'null' => false
        ]);
        $table->addColumn('description', 'text', [
          'default' => null,
          'limit' => 'LONG_TEXT',
          'null' => true
        ]);
        $table->addColumn('icon', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modfiied', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
