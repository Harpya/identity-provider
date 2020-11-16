<?php

declare(strict_types=1);

namespace Harpya\IP\Models;

use Phalcon\Mvc\Model;

class SessionEstablished extends Model
{
    const SESSION_PENDENT = 1;
    const SESSION_ACTIVE = 2;
    const SESSION_INACTIVE = 3;

    public function initialize()
    {
        $this->setSource('sessions_established');
        $this->belongsTo('user_id', 'User', 'id');
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

    public function setStatus($status)
    {
        $this->status = $status;
        $this->updated_at = 'now()';
        return $this;
    }
}
