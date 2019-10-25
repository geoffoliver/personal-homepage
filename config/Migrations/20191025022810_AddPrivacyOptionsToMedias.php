<?php
use Migrations\AbstractMigration;

class AddPrivacyOptionsToMedias extends AbstractMigration
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

        $table->addColumn('public', 'boolean', [
            'after' => 'description',
            'default' => true,
            'null' => false,
        ]);

        $table->addColumn('allow_comments', 'boolean', [
            'default' => true,
            'null' => false,
            'after' => 'public'
        ]);

        $table->update();
    }
}
