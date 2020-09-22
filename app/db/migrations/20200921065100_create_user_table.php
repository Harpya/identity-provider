<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserTable extends AbstractMigration
{
    public function up(): void
    {
        /**
         * Status:
         *  1 = unverified
         *  2 = verified, active
         *  3 = inactive
         */
        $this->table('users')
            ->addColumn('status', 'smallinteger', ['default' => 1])
            ->addColumn('email', 'string', ['length' => 200, 'null' => false])
            ->addColumn('authentication_string', 'string', ['length' => 200, 'null' => false])
            ->addTimestampsWithTimezone()
            ->addIndex(['email'], [
                'unique' => true,
                'name' => 'idx_users_email'])
            ->save();
    }

    public function down(): void
    {
        $this->table('users')
                ->drop()
                ->save();
    }
}
