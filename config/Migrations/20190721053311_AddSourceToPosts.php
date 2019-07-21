<?php
use Migrations\AbstractMigration;

class AddSourceToPosts extends AbstractMigration
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

        $table->addColumn('source', 'text', [
            'default' => null,
            'null' => true,
            'after' => 'content'
        ]);

        $table->update();
    }
}
