<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTableResetPasswordTables extends AbstractMigration
{
    public function up(): void
    {
        $this->table('password_reset_requests')
        ->addColumn('user_id', 'biginteger', ['null' => true])
        ->addColumn('status', 'integer', ['default' => 1])
        ->addColumn('expires_at', 'integer', ['null' => false])
        ->addColumn('token', 'string', ['null' => false, 'length' => 100])
        ->addTimestamps()
        ->save();
    }

    public function down() : void
    {
        $this->table('password_reset_requests')
        ->drop()
        ->save();
    }
}
