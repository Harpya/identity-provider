<?php

include_once __DIR__ . '/bootstrap.php';

class SignupTest extends End2EndTestBase
{
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

        $pass = hash('sha256', '123');
        try {
            $submitted = static::getClient()->request('post', static::getURL('/auth/signup'), [
                'cookies' => $jar,
                'form_params' => [
                    $csrfKey => $csrfToken,
                    'email' => 'invalidEmail',
                    'password' => $pass,
                    'confirm_password' => $pass,
                    'accept_terms' => 'yes'
                ]
            ]);
            echo "\n\nResponse = " . $submitted->getBody()->getContents() . "\n\n";
            $this->assertTrue(false, 'Expected this test fail: expected httpCode 400');
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
        }
    }

    /** @test */
    public function testSignupInvalidSmallPassword()
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
                    'email' => 'validEmail@domain.com',
                    'password' => '123',
                    'accept_terms' => 'yes'
                ]
            ]);
            echo "\n\nResponse = " . $submitted->getBody()->getContents() . "\n\n";
            $this->assertTrue(false, 'Expected this test fail: expected httpCode 400');
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
            $text = $ex->getResponse()->getBody()->getContents();
            $this->assertTrue(strpos($text, 'Small password') !== false);
        }
    }

    /** @test */
    public function testSignupPasswordDoesNotMatchConfirmation()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);
        $pass = hash('sha256', '123');
        try {
            $submitted = static::getClient()->request('post', static::getURL('/auth/signup'), [
                'cookies' => $jar,
                'form_params' => [
                    $csrfKey => $csrfToken,
                    'email' => 'validEmail@domain.com',
                    'password' => $pass,
                    'confirm_password' => 'abcdefghjkl',
                    'accept_terms' => 'yes'
                ]
            ]);
            $this->assertTrue(false, 'Expected this test fail: expected httpCode 400');
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
            $text = $ex->getResponse()->getBody()->getContents();
            $this->assertTrue(strpos($text, 'password confirmation does not match') !== false);
        }
    }

    /** @test */
    public function testSignupInvalidNotAcceptTerms()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);
        $pass = hash('sha256', '123');
        try {
            $submitted = static::getClient()->request('post', static::getURL('/auth/signup'), [
                'cookies' => $jar,
                'form_params' => [
                    $csrfKey => $csrfToken,
                    'email' => 'validEmail@domain.com',
                    'password' => $pass,
                    'confirm_password' => $pass,
                ]
            ]);
            $this->assertTrue(false, 'Expected this test fail: expected httpCode 400');
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
            $text = $ex->getResponse()->getBody()->getContents();
            $this->assertTrue(strpos($text, 'It is necessary accept the terms') !== false);
        }
    }

    /** @test */
    public function testSignupOk()
    {
        static::generateNewEmail();

        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);

        $pass = hash('sha256', '123');
        $submitted = static::getClient()->request('post', static::getURL('/auth/signup'), [
            'cookies' => $jar,
            'form_params' => [
                $csrfKey => $csrfToken,
                'email' => static::$currentEmail,
                'password' => $pass,
                'confirm_password' => $pass,
                'accept_terms' => 'yes'
            ]
        ]);
        $this->assertEquals(200, $submitted->getStatusCode());
        $text = $submitted->getBody()->getContents();
        $this->assertTrue(strpos($text, 'created with success') !== false);
        $this->assertTrue(strpos($text, static::$currentEmail) !== false);
    }

    /**
     * @test
     * @depends testSignupOk
     */
    public function testSignupEmailAlreadyExists()
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
                    'email' => static::$currentEmail,
                    'password' => hash('sha256', '123'),
                    'accept_terms' => 'yes'
                ]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            $text = $ex->getResponse()->getBody()->getContents();
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
            $this->assertTrue(strpos($text, 'Error creating') !== false);
            $this->assertTrue(strpos($text, static::$currentEmail) !== false);
        }

        $this->assertFalse(false);
    }
}
