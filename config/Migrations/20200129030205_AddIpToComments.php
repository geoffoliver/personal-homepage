<?php
use Migrations\AbstractMigration;

class AddIpToComments extends AbstractMigration
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
        $table->addColumn('ip', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
            'after' => 'import_source'
        ]);
        $table->update();
    }
}
