<?php
declare(strict_types=1);

namespace Harpya\IP;

use \Harpya\IP\Exceptions\ApplicationExection;
use \Phalcon\Di;

class Application
{
    protected static $instance;

    protected $di;

    public static function getInstance($di = null)
    {
        if (!static::$instance) {
            // if (is_null($di)) {
            //     throw new ApplicationExection('Falat error - incorrect use of getApplication');
            // }
            static::$instance = new Application($di);
        }
        return static::$instance;
    }

    public function __construct($di = null)
    {
        if ($di) {
            $this->di = $di;
        }
    }

    /**
     * Retrieve the final target, from original initial request
     */
    public static function getTargetURL()
    {
        return getenv('APP_DEFAULT_TARGET_AFTER_LOGIN');
    }

    public static function decodeAuthRequestToken($t) : array
    {
        // $pack = base64_encode(json_encode([
        //     'authorize' => 'http://localhost:1991/authorize',
        //     'application_id' => 'A0D47F',
        //     'application_token' => 'abcdef0123456789',
        //     'client_ip' => $_SERVER['REMOTE_ADDR']
        // ]));

        $b64s = \base64_decode($t);

        if (!is_string($b64s)) {
            throw new \Harpya\IP\Exceptions\RequestException('Invalid decoded token');
        }

        $jsonDecoded = \json_decode($b64s, true);

        if (!is_array($jsonDecoded)) {
            throw new \Harpya\IP\Exceptions\RequestException('Invalid token contents');
        }

        return $jsonDecoded;
    }

    // $pack = base64_encode(json_encode([
    //     'authorize' => 'http://localhost:1991/authorize',
    //     'application_id' => 'A0D47F',
    //     'application_token' => 'abcdef0123456789',
    //     'client_ip' => $_SERVER['REMOTE_ADDR']
    // ]));

    // 2. Validate data
    //      application_id with application_token
    //      client_ip should match
    //      'authorize' URL should match with condigured whitelist

    public static function validateAuthRequestToken(array $request)
    {
        // 1. Skipped application validation. (TODO)

        // echo '<pre>';
        // print_r($_SERVER);
        // exit;

        // 2. Checking Client IP - skipping due to different gateway
        // if ($request['client_ip'] !== $_SERVER['REMOTE_ADDR']) {
        //     throw new \Harpya\IP\Exceptions\RequestException('IP addressed does not matches');
        // }

        // 3. Skipping "authorize" URL
    }
}
