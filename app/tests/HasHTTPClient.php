<?php

trait HasHTTPClient
{
    protected static $httpClients = [];

    public static function getClient($name = 'default')
    {
        if (!isset(self::$httpClients[$name])) {
            self::$httpClients[$name] = new \GuzzleHttp\Client();
        }

        return self::$httpClients[$name] ;
    }

    public static function getURL($url = '/')
    {
        $host = getenv('WEB_SERVER_BASE_URL');
        if (substr($host, -1) == '/') {
            $host = substr($host, 0, -1);
        }

        if (substr($url, 0, 1) !== '/') {
            $url = '/' . $url;
        }

        $response = $host . $url;
        return $response;
    }
}
