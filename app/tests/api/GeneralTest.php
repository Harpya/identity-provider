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
        $response = $client->request('GET', 'http://webserver/api/v1/auth');
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    

}