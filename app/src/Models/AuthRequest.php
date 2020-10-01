<?php

declare(strict_types=1);

namespace Harpya\IP\Models;

use Phalcon\Mvc\Model;

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
}
