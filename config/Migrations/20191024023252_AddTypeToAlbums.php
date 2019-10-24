<?php
use Migrations\AbstractMigration;

class AddTypeToAlbums extends AbstractMigration
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
        $table = $this->table('albums');
        $table->addColumn('type', 'string', [
            'default' => 'photos',
            'null' => true,
            'after' => 'cover_photo'
        ]);
        $table->update();
    }
}
