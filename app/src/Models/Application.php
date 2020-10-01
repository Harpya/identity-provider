<?php

declare(strict_types=1);

namespace Harpya\IP\Models;

use Phalcon\Mvc\Model;

class Application extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public function initialize()
    {
        $this->setSource('applications');
    }

    /**
     *
     */
    public static function findRegisteredApplication($appID, $secret, $ip = false)
    {
        $finalHash = static::getSecretHash($appID, $secret);

        $resp = static::findFirst([
            'status=1 AND app_id = :app_id: AND secret_hash = :secret_hash:',
            'bind' => ['app_id' => $appID, 'secret_hash' => $finalHash]
        ]);
        return $resp;
    }

    /**
     *
     */
    public static function getSecretHash($appID, $secret)
    {
        return hash('sha256', $appID . ':' . $secret);
    }
}
