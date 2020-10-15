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

    /**
     * Create a new instance of SessionEstablished Model, having the
     * $userID as input.
     *
     * @param $userID int
     * @return SessionEstablished
     */
    public static function buildFromUserID($userID): SessionEstablished
    {
        $obj = new SessionEstablished();

        $obj->user_id = $userID;
        $obj->ip_address = $_SERVER['REMOTE_ADDR'];
        $obj->app_id = 0;

        return $obj;
    }
}
