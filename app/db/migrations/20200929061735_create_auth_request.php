<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAuthRequest extends AbstractMigration
{
    public function up(): void
    {
        $this->table('auth_requests')
            ->addColumn('app_id', 'integer', ['null' => false])
            ->addColumn('valid_until', 'biginteger', ['null' => false])

            ->addColumn('status', 'smallinteger', ['default' => 1])
            ->addColumn('token', 'string', ['length' => 100, 'null' => true])

            ->addColumn('url_after_login', 'string', ['length' => 100, 'null' => true])
            ->addColumn('url_authorize', 'string', ['length' => 100, 'null' => true])
            ->addColumn('ip_address', 'string', ['length' => 20, 'null' => true])
            ->addTimestampsWithTimezone()
            ->save();
    }

    public function down(): void
    {
        $this->table('auth_requests')
        ->drop()
        ->save();
    }
}
