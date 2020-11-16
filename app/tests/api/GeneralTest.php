<?php

use PHPUnit\Framework\TestCase;

class GeneralTest extends TestCase
{
    /** @test */
    public function testRootPage()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://webserver/');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function testAuthRootRequest()
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', 'http://webserver/api/v1/auth');
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(404, $ex->getResponse()->getStatusCode());
        }
    }
}
