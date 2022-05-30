<?php
use Migrations\AbstractMigration;

class AddIsLinkToPost extends AbstractMigration
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
        $table->addColumn('is_link', 'boolean', [
            'default' => false,
            'null' => false,
            'after' => 'embeds'
        ]);
        $table->update();
    }
}
