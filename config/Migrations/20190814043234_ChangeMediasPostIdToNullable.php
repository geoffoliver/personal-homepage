<?php
use Migrations\AbstractMigration;

class ChangeMediasPostIdToNullable extends AbstractMigration
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

        $table = $this->table("medias");

        $table->removeColumn('post_id');
        $table->addColumn('post_id', 'uuid', [
            'null' => true,
            'default' => null,
            'after' => 'id'
        ]);

        $table->update();
    }
}
