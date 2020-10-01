<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSessionEstablished extends AbstractMigration
{
    public function up(): void
    {
        $this->table('sessions_established')
            ->addColumn('app_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('valid_until', 'biginteger', ['null' => false])

            ->addColumn('status', 'smallinteger', ['default' => 1])
            ->addColumn('token', 'string', ['length' => 100, 'null' => true])

            ->addColumn('ip_address', 'string', ['length' => 20, 'null' => true])
            ->addTimestampsWithTimezone()
            ->save();
    }

    public function down(): void
    {
        $this->table('sessions_established')
        ->drop()
        ->save();
    }
}
