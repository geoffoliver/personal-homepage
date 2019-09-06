<?php
use Migrations\AbstractMigration;

class AddUrlToComments extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('comments');
        $table->addColumn('url', 'string', [
            'default' => null,
            'null' => true,
            'after' => 'display_name'
        ]);
        $table->update();
    }
}
