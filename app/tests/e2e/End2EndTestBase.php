<?php

namespace e2e;

use PHPUnit\Framework\TestCase;

class End2EndTestBase extends TestCase
{
    use \HasHTTPClient;

    public static $currentEmail = '';
    public static $currentPassword = '';

    protected function generateNewEmail()
    {
        static::$currentEmail = 'test_' . microtime(true) . substr(hash('sha256', time() . random_bytes(10)), 0, 10) . '@domain.com';
    }

    protected function generateNewPassword()
    {
        $parms = [
            'capitals' => range('A', 'Z'),
            'small' => range('a', 'z'),
            'symbols' => ['.', '*', '@', '!', '?', '#', '$', '%', '^', '&', '(', ')', '/', '\\', '<', '>', '~']
        ];

        $base = hash('sha256', time());
        $i = random_int(0, 16);

        $pass = substr($base, $i, 10);

        foreach ($parms as $arr) {
            $i = random_int(0, count($arr) - 1);
            $pos = random_int(0, strlen($pass) - 1);
            $pass[$pos] = $arr[$i];
        }

        static::$currentPassword = $pass;
    }

    protected static function getCsrfToken($html)
    {
        $pattern = '/<input type=\'hidden\'\s*name=\'(\w+)\'\s+value=\'(\w+)\'\s*\/>/';
        $csrfArray = [];
        preg_match($pattern, $html, $csrfArray);

        assert(is_array($csrfArray));
        assert(3 === count($csrfArray));

        return [$csrfArray[1], $csrfArray[2]];
    }
}
