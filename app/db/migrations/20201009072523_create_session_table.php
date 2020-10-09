<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSessionTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up(): void
    {
        $this->table('sessions', ['id' => false, 'primary_key' => ['sess_id']])
            ->addColumn('sess_id', 'string')
            ->addColumn('sess_data', 'string', ['length' => 4096])
            ->addColumn('last_activity', 'biginteger')
            ->addColumn('ip', 'string', ['null' => true, 'length' => 15])
            ->save();
    }

    public function down() : void
    {
        $this->table('sessions')
        ->drop()
        ->save();
    }
}
