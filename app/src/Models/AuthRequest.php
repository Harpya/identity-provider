<?php

declare(strict_types=1);

namespace Harpya\IP\Models;

use Phalcon\Mvc\Model;
use \Harpya\IP\Constants;
use \Harpya\SDK\IdentityProvider\Utils;

class AuthRequest extends Model
{
    const STATUS_CREATED = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_FINISHED = 3;
    const STATUS_ERROR = 9;

    public function initialize()
    {
        $this->setSource('auth_requests');
    }

    /**
     *
     */
    public function changeStatus($status)
    {
        $this->status = $status;
        $this->updated_at = 'now()';

        $ttl = \getenv(Constants::CONFIG_SESSION_TTL);
        $this->valid_until = time() + Utils::getTTL(10); // 10 minutes
        $this->save();
    }
}
