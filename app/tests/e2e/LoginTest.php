<?php

use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../HasHTTPClient.php';

class LoginTest extends TestCase
{
    use HasHTTPClient;

    /** @test */
    public function testErrorSignupInvalidCsrfToken()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        try {
            $submitted = static::getClient()->request('post', static::getURL('/auth/signup'), [
                'cookies' => $jar
            ]);
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
            // } catch (GuzzleHttp\Exception\ServerException $ex) {
        //     $this->assertEquals(500, $ex->getResponse()->getStatusCode());
        }
    }

    /**
     * @test
     * @todo
    */
    public function testNoEmailInformed()
    {
        // assertions
        $this->assertFalse(false);
    }

    /**
     * @test
     * @todo
     */
    public function testNoPasswordInformed()
    {
        // assertions
        $this->assertFalse(false);
    }

    /**
     * @test
     * @todo
     */
    public function testEmailDoesNotExists()
    {
        // assertions
        $this->assertFalse(false);
    }

    /**
     * @test
     * @todo
     */
    public function testEmailPasswordDoesNotMatch()
    {
        // assertions
        $this->assertFalse(false);
    }

    /**
     * @test
     * @todo
     */
    public function testInvalidLoginMultipleTries()
    {
        // assertions
        $this->assertFalse(false);
    }

    /**
     * @test
     * @todo
     */
    public function testLoginOk()
    {
        // assertions
        $this->assertFalse(false);
    }

    /**
     * @test
     * @todo
     */
    public function testHitLoginAlreadyAuthenticated()
    {
        // assertions
        $this->assertFalse(false);
    }
}
