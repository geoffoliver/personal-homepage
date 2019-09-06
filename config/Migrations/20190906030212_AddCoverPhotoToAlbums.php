<?php
use Migrations\AbstractMigration;

class AddCoverPhotoToAlbums extends AbstractMigration
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
        $table->addColumn('cover_photo', 'uuid', [
            'default' => null,
            'null' => true,
            'after' => 'description'
        ]);
        $table->update();
    }
}
