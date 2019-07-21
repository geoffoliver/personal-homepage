<?php
use Migrations\AbstractMigration;

class AddFieldsToMedias extends AbstractMigration
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

        $table->addColumn('name', 'string', [
            'null' => true,
            'default' => null,
            'after' => 'mime'
        ]);

        $table->addColumn('description', 'text', [
            'null' => true,
            'default' => null,
            'after' => 'name'
        ]);

        $table->addColumn('size', 'integer', [
            'null' => false,
            'default' => 0,
            'after' => 'description'
        ]);

        $table->update();
    }
}
