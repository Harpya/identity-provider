<?php
declare(strict_types=1);

namespace Harpya\IP\Lib;

use PDO;

class DBSessionAdapter implements \SessionHandlerInterface
{
    protected $sessionID = false;
    protected $sessionsMarkedToRemove = [];

    protected $connection;

    protected $tablename;

    protected $configs;

    public function __construct($connection, $tablename = 'sessions', array $configs = [])
    {
        $this->connection = $connection;
        $this->tablename = $tablename;
        $this->configs = array_merge([
            'col_sessid' => 'sess_id',
            'col_data' => 'sess_data',
            'col_last_activity' => 'last_activity',
            'col_user_id' => 'user_id',
            'col_addr_id' => 'ip',
        ], $configs);
    }

    public function open($save_path, $sess_name)
    {
        return true;
    }

    /**
     *
     */
    public function read($sess_id)
    {
        $this->evalSessionID($sess_id);

        $table = $this->tablename;
        $col_sessid = $this->configs['col_sessid'];
        $col_data = $this->configs['col_data'];
        $query = $this->connection->query("
            SELECT * FROM {$table} 
            WHERE {$col_sessid} = '$sess_id' 
            LIMIT 1
        ");

        $session = $query->fetch(PDO::FETCH_ASSOC);

        $this->exists = (false === empty($session));

        return $this->exists ? base64_decode($session[$col_data]) : '';
    }

    public function write($sess_id, $data)
    {
        $oldSessionID = $sess_id;
        $this->evalSessionID($sess_id);

        // If "original", "old" SessionID is marked for deletion, then remove the data from DB,
        // to eliminate eventual sensitive data from previous session
        if (isset($this->sessionsMarkedToRemove[$oldSessionID])) {
            $this->removeSession($oldSessionID);

            // if there was no change of sessionID, then just return to skip next steps, which
            // lead to insert (recreate) the "old" Session data on DB.
            if ($oldSessionID === $sess_id) {
                return true;
            }
        }

        $table = $this->tablename;
        $col_sessid = $this->configs['col_sessid'];
        $col_data = $this->configs['col_data'];
        $col_la = $this->configs['col_last_activity'];
        $col_user_id = $this->configs['col_user_id'];
        $col_addr_id = $this->configs['col_addr_id'];

        $sData = \unserialize($data);

        $data = base64_encode($data);
        $time = time();

        if (isset($this->userID)) {
            $userID = $this->userID;
        } else {
            $userID = 0;
        }
        if (isset($this->ip)) {
            $ip = $this->ip;
        } else {
            $ip = '-';
        }

        if ($userID && $ip) {
            $sql = "
            INSERT INTO {$table}
            ({$col_sessid}, {$col_data}, {$col_la},{$col_user_id}, {$col_addr_id})
            VALUES ('{$sess_id}','{$data}', {$time}, {$userID}, '{$ip}')
            ON CONFLICT ({$col_sessid}) DO UPDATE   SET 
            {$col_data} = '{$data}',
            {$col_la} = {$time},
            {$col_user_id} = {$userID},
            {$col_addr_id} = '{$ip}'    
        ";
        } else {
            $sql = "
        INSERT INTO {$table}
        ({$col_sessid}, {$col_data}, {$col_la})
        VALUES ('{$sess_id}','{$data}', {$time})
        ON CONFLICT ({$col_sessid}) DO UPDATE   SET 
        {$col_data} = '{$data}',
        {$col_la} = {$time}
    ";
        }

        $this->connection->query($sql);

        $this->exists = true;
        return true;
    }

    public function close()
    {
        return true;
    }

    public function destroy($sess_id)
    {
        $table = $this->tablename;
        $col_sessid = $this->configs['col_sessid'];

        $this->connection->query("DELETE FROM {$table} WHERE {$col_sessid}='{$sess_id}'");

        return true;
    }

    public function gc($lifetime)
    {
        $table = $this->tablename;
        $col_la = $this->configs['col_last_activity'];
        $expired_time = time() - $lifetime;

        $this->connection->query("DELETE FROM {$table} WHERE {$col_la} <= {$expired_time}");

        return true;
    }

    // Utility methods

    /**
     * Just switch the $sessionID if there is any value on $this->sessionID
     */
    protected function evalSessionID(&$sessionID)
    {
        if ($this->sessionID) {
            $sessionID = $this->sessionID;
        }
    }

    /**
     *
     */
    public function getSessionID()
    {
        if ($this->sessionID) {
            return $this->sessionID;
        } else {
            return session_id();
        }
    }

    public function setSessionID($sessionID)
    {
        $this->sessionID = $sessionID;
        return $this;
    }

    /**
     *
     */
    public function markToRemoveSession($sessionID)
    {
        $this->sessionsMarkedToRemove[$sessionID] = 1;
    }

    /**
     * Remove the record having the informed $sessionID
     */
    protected function removeSession($sessionID)
    {
        $table = $this->tablename;
        $col_sessid = $this->configs['col_sessid'];

        $this->connection->query("DELETE FROM {$table} WHERE {$col_sessid} = '{$sessionID}'");
    }
}
