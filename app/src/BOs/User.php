<?php

declare(strict_types=1);

namespace Harpya\IP\BOs;

use \Harpya\IP\VOs\InfoLoginVO;
use \Harpya\IP\Models\SessionEstablished;

class User
{
    // protected $app;
    protected $email;

    public function __construct($email = false)
    {
        // $this->app = \Harpya\IP\Application::getInstance();
        $this->email = $email;
    }

    public function load()
    {
        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getInfoLastLogin()
    {
        $query = \Harpya\IP\Application::getInstance()->modelsManager->createQuery(
            'SELECT * FROM \Harpya\IP\Models\SessionEstablished as sess, 
                \Harpya\IP\Models\User as usr WHERE
                usr.id = sess.user_id 
                AND usr.email = :email:
                ORDER BY sess.created_at DESC'
            );

        // Execute the query returning a result if any
        $sessionCursor = $query->execute([
            'email' => $this->getEmail()
        ]);
        $arrRegs = $sessionCursor->jsonSerialize();

        $lastLogin = new InfoLoginVO();

        if (count($arrRegs) > 0) {
            $firstRecord = reset($arrRegs);
            $tm = $firstRecord['harpya\IP\Models\SessionEstablished']->created_at;
            $lastLogin->set('dateTime', $tm);
        }

        return $lastLogin;
    }
}
