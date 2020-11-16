<?php

declare(strict_types=1);

namespace Harpya\IP\Models;

use Phalcon\Mvc\Model;

class PasswordResetRequest extends Model
{
    public function initialize()
    {
        $this->setSource('password_reset_requests');
        $this->belongsTo('user_id', 'User', 'id');
    }

    /**
     * Create a new instance of PasswordResetRequest Model, having the
     * $userID as input.
     *
     * @param $userID int
     * @return PasswordResetRequest
     */
    public static function buildFromUserID($userID): PasswordResetRequest
    {
        $obj = new PasswordResetRequest();

        $obj->user_id = $userID;
        $obj->expires_at = time() + (60 * 15); // expires in 15 minutes
        $obj->token = \Harpya\SDK\Utils::generateRandomToken();

        return $obj;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->updated_at = 'now()';
        return $this;
    }
}
