<?php
use Migrations\AbstractMigration;

class AddAlbumIdToMedias extends AbstractMigration
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

        $table->addColumn('album_id', 'uuid', [
            'default' => null,
            'null' => true,
            'after' => 'post_id',
        ]);

        $table->addIndex(['album_id']);

        $table->update();
    }
}
