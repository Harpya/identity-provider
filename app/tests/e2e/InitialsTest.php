<?php

use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../HasHTTPClient.php';

class InitialsTest extends TestCase
{
    use HasHTTPClient;

    /** @test */
    public function testShowInitialPage()
    {
        $response = static::getClient()->request('GET', 'http://webserver/');
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();
        $this->assertTrue(strpos($html, '<form action="/auth/login" method="post" name="login">') !== false);
    }

    /** @test */
    public function testShowInitialPageWithDifferentUrl()
    {
        $response = static::getClient()->request('GET', 'http://webserver/auth/signup');
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();
        $this->assertTrue(strpos($html, '<form action="/auth/login" method="post" name="login">') !== false);
    }
}
