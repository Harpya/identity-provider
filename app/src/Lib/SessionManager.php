<?php
declare(strict_types=1);

namespace Harpya\IP\Lib;

use Phalcon\Session\Manager;

class SessionManager extends Manager
{
    protected $userID = -1;

    /**
     * Get the value of userID
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Set the value of userID
     *
     * @return  self
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Removes a given session.
     *
     * @param string $sessionID
     * @return void
     */
    public function removeSession(string $sessionID)
    {
        $sql = "DELETE FROM sessions WHERE sess_id='$sessionID'";
        $app = \Harpya\IP\Application::getInstance();
        $resp = $app->db->query($sql);
    }
}
