<?php

use Phinx\Seed\AbstractSeed;

class SeedApplications extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'status' => 1,
                'name' => 'App Ok',
                'app_id' => 'a000001',
                'secret_hash' => '52dfd5009b94034a20a6ea15531c4edb57a2eb3e2df015972aa0d5ea59d86d90', // for secret = c1092df28283899edc175e7954eda52b67a58ca132c0750ac336d25e4ad08587
                'url_after_login' => 'http://localhost:1991/t/',
                'url_authorize' => 'http://host.docker.internal:1991/authorize',
                'ip_address' => '192.168.150.6',
            ],
            [
                'status' => 0,
                'name' => 'App inactive',
                'app_id' => 'a0000x1',
                'secret_hash' => 'f5ebe387bad47e6709eedd9e56b536544c652ea74be74a2ccb9bbaf3b5feb7ef', // for secret = c1092df28283899edc175e7954eda52b67a58ca132c0750ac336d25e4ad08587
                'url_after_login' => 'http://localhost:1991/xpto',
                'url_authorize' => 'http://host.docker.internal:1991/authorize',
                'ip_address' => '192.168.150.6',
            ],
            [
                'status' => 1,
                'name' => 'App invalid ip',
                'app_id' => 'a0000x2',
                'secret_hash' => '30d783a06b6cc9b0f0533862f5ef8fb2cfab79b526cd8868b3544863f7bb6a93', // for secret = c1092df28283899edc175e7954eda52b67a58ca132c0750ac336d25e4ad08587
                'url_after_login' => 'http://localhost:1991/xpto',
                'url_authorize' => 'http://host.docker.internal:1991/authorize',
                'ip_address' => '192.168.0.1',
            ],
            [
                'status' => 1,
                'name' => 'App invalid pair app_id/secret',
                'app_id' => 'a0000x3',
                'secret_hash' => 'invalid-hash',
                'url_after_login' => 'http://localhost:1991/xpto',
                'url_authorize' => 'http://host.docker.internal:1991/authorize',
                'ip_address' => '192.168.150.6',
            ],
            [
                'status' => 1,
                'name' => 'App ok 2 (2nd base url)',
                'app_id' => 'a000002',
                'secret_hash' => '389bda20d53ce90f88d299e2c724d85d7d8cf1634f31656a298064d8b9548ff0', // for secret = c1092df28283899edc175e7954eda52b67a58ca132c0750ac336d25e4ad08587
                'url_after_login' => 'http://localhost:1991/flow2',
                'url_authorize' => 'http://host.docker.internal:1991/authorize',
                'ip_address' => '192.168.150.6',
            ],
        ];

        $apps = $this->table('applications');

        $apps->truncate();

        $apps->insert($data)
              ->saveData();
    }
}
