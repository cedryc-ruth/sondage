<?php
use Migrations\AbstractMigration;

class CreateGroups extends AbstractMigration
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
        $table = $this->table('groups');
        $table->addColumn('nom', 'string', [
            'default' => null,
            'limit' => 60,
            'null' => false,
        ]);
        $table->addColumn('couleur', 'string', [
            'default' => null,
            'limit' => 60,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();
    }
}
