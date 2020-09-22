<?php

declare(strict_types=1);

namespace Harpya\IP\Models;

use Phalcon\Mvc\Model;

class User extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_VERIFICATION_PENDING = 1;
    const STATUS_ACTIVE = 2;

    public function initialize()
    {
        $this->setSource('users');
    }
}
