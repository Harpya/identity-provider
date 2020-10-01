<?php

declare(strict_types=1);

namespace Harpya\IP\Models;

use Phalcon\Mvc\Model;

class SessionEstablished extends Model
{
    public function initialize()
    {
        $this->setSource('sessions_established');
    }
}
