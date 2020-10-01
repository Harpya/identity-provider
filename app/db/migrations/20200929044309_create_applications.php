<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateApplications extends AbstractMigration
{
    public function up(): void
    {
        $this->table('applications')
            ->addColumn('status', 'smallinteger', ['default' => 1])
            ->addColumn('name', 'string', ['length' => 100, 'null' => false])
            ->addColumn('app_id', 'string', ['length' => 60, 'null' => false])
            ->addColumn('secret_hash', 'string', ['length' => 100, 'null' => false])
            ->addColumn('url_authorize', 'string', ['length' => 100, 'null' => true])
            ->addColumn('url_after_login', 'string', ['length' => 100, 'null' => true])
            ->addColumn('ip_address', 'string', ['length' => 20, 'null' => true])
            ->addTimestampsWithTimezone()
            ->addIndex(
                        [
                            'app_id'
                        ],
                        [
                            'unique' => true,
                            'name' => 'idx_apps'
                        ]
            )
            ->save();
    }

    public function down(): void
    {
        $this->table('applications')
        ->drop()
        ->save();
    }
}
