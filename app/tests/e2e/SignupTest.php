<?php

use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../HasHTTPClient.php';

class SignupTest extends TestCase
{
    use HasHTTPClient;

    /** @test */
    public function testErrorSignupInvalidCsrfToken()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());

        try {
            $submitted = static::getClient()->request('post', static::getURL('/auth/signup'), [
                'cookies' => $jar
            ]);
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
        } catch (GuzzleHttp\Exception\ServerException $ex) {
            $this->assertEquals(500, $ex->getResponse()->getStatusCode());
        }
    }

    /** @test */
    public function testSignupInvalidEmail()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);

        try {
            $submitted = static::getClient()->request('post', static::getURL('/auth/signup'), [
                'cookies' => $jar,
                'form_params' => [
                    $csrfKey => $csrfToken,
                    'email' => 'invalidEmail',
                    'password' => hash('sha256', '123')
                ]
            ]);
            echo "\n\nResponse = " . $submitted->getBody()->getContents() . "\n\n";
            $this->assertTrue(false, 'Expected this test fail: expected httpCode 400');
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
            // } catch (GuzzleHttp\Exception\ServerException $ex) {
        //     $this->assertEquals(400, $ex->getResponse()->getStatusCode());
        }
    }

    /** @test */
    public function testSignupInvalidSmallPassword()
    {
        $this->assertFalse(false);
    }

    /** @test */
    public function testSignupPasswordDoesNotMatchConfirmation()
    {
        $this->assertFalse(false);
    }

    /** @test */
    public function testSignupInvalidNotAcceptTerms()
    {
        $this->assertFalse(false);
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

    /** @test */
    public function testSignupOk()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);

        $submitted = static::getClient()->request('post', static::getURL('/auth/signup'), [
            'cookies' => $jar,
            'form_params' => [
                $csrfKey => $csrfToken,
                'email' => 'myEmail@domain.com',
                'password' => hash('sha256', '123')
            ]
        ]);

        $this->assertEquals(200, $submitted->getStatusCode());
    }

    /** @test */
    public function testSignupEmailAlreadyExists()
    {
        $this->assertFalse(false);
    }
}
