<?php
use Migrations\AbstractMigration;

class AddEmbedsToPosts extends AbstractMigration
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
        $table->addColumn('embeds', 'text', [
            'default' => null,
            'null' => true,
            'after' => 'import_source'
        ]);
        $table->update();
    }
}
