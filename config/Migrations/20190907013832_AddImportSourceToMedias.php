<?php
use Migrations\AbstractMigration;

class AddImportSourceToMedias extends AbstractMigration
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
        $table = $this->table('medias');
        $table->addColumn('import_source', 'string', [
            'default' => null,
            'null' => true,
            'after' => 'original_filename'
        ]);
        $table->update();
    }
}
