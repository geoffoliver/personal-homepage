<?php
use Migrations\AbstractMigration;

class AddSquareThumbnailToMedias extends AbstractMigration
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
        $table->addColumn('square_thumbnail', 'text', [
            'null' => true,
            'default' => null,
            'after' => 'thumbnail'
        ]);
        $table->update();
    }
}
