<?php

namespace e2e;

use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/bootstrap.php';

class InitialsTest extends End2EndTestBase
{
    /** @test */
    public function testShowInitialPage()
    {
        $response = static::getClient()->request('GET', 'http://webserver/');
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();
        // print_r($html);
        // exit;
        $this->assertTrue(strpos($html, '<form action="/auth/login" method="post" name="frm-login">') !== false);
    }

    /** @test */
    public function testShowInitialPageWithDifferentUrl()
    {
        $response = static::getClient()->request('GET', 'http://webserver/auth/signup');
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();
        $this->assertTrue(strpos($html, '<form action="/auth/login" method="post" name="frm-login">') !== false);
    }
}
