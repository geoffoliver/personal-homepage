<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AllowNullContentOnPosts extends AbstractMigration
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

        $table = $this->table("posts");

        $table->removeColumn('content');

        $table->addColumn('content', 'text', [
            'null' => true,
            'default' => null,
            'after' => 'name',
            'limit' => MysqlAdapter::TEXT_LONG
        ]);

        $table->update();

    }
}
