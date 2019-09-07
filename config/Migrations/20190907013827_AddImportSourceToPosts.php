<?php
use Migrations\AbstractMigration;

class AddImportSourceToPosts extends AbstractMigration
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
        $table = $this->table('posts');
        $table->addColumn('import_source', 'string', [
            'default' => null,
            'null' => true,
            'after' => 'source'
        ]);
        $table->update();
    }
}
