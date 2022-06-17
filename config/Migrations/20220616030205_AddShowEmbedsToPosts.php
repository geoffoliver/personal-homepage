<?php
use Migrations\AbstractMigration;

class AddShowEmbedsToPosts extends AbstractMigration
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
        $table->addColumn('show_embeds', 'boolean', [
            'default' => true,
            'null' => false,
            'after' => 'embeds'
        ]);
        $table->update();
    }
}
