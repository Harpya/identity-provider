<?php

include_once __DIR__ . '/bootstrap.php';

class LoginTest extends End2EndTestBase
{
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

    /** @test */
    public function testSignupOk()
    {
        $this->generateNewEmail();
        $this->generateNewPassword();

        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);

        $submitted = static::getClient()->request('post', static::getURL('/auth/signup'), [
            'cookies' => $jar,
            'form_params' => [
                $csrfKey => $csrfToken,
                'email' => static::$currentEmail,
                'password' => static::$currentPassword,
                'confirm_password' => static::$currentPassword,
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
    public function testNoEmailInformed()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);

        try {
            $submitted = static::getClient()->request('post', static::getURL('/auth/login'), [
                'cookies' => $jar,
                'form_params' => [
                    $csrfKey => $csrfToken,
                    'email' => '',
                    'password' => static::$currentPassword,
                ]
            ]);
            echo "\n\nResponse = " . $submitted->getBody()->getContents() . "\n\n";
            $this->assertTrue(false, 'Expected this test fail: expected httpCode 400');
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
            $text = $ex->getResponse()->getBody()->getContents();
            $this->assertTrue(strpos($text, 'Email not informed') !== false);
        }
    }

    /**
     * @test
     * @todo
     */
    public function testNoPasswordInformed()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);

        try {
            $submitted = static::getClient()->request('post', static::getURL('/auth/login'), [
                'cookies' => $jar,
                'form_params' => [
                    $csrfKey => $csrfToken,
                    'email' => static::$currentEmail,
                    'password' => '',
                ]
            ]);
            echo "\n\nResponse = " . $submitted->getBody()->getContents() . "\n\n";
            $this->assertTrue(false, 'Expected this test fail: expected httpCode 400');
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
            $text = $ex->getResponse()->getBody()->getContents();
            $this->assertTrue(strpos($text, 'Password not informed') !== false);
        }
    }

    /**
     * @test
     * @todo
     */
    public function testEmailDoesNotExists()
    {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);

        try {
            $submitted = static::getClient()->request('post', static::getURL('/auth/login'), [
                'cookies' => $jar,
                'form_params' => [
                    $csrfKey => $csrfToken,
                    'email' => static::$currentEmail,
                    'password' => 'abc',
                ]
            ]);
            echo "\n\nResponse = " . $submitted->getBody()->getContents() . "\n\n";
            $this->assertTrue(false, 'Expected this test fail: expected httpCode 400');
        } catch (GuzzleHttp\Exception\ClientException $ex) {
            $this->assertEquals(400, $ex->getResponse()->getStatusCode());
            $text = $ex->getResponse()->getBody()->getContents();
            $this->assertTrue(strpos($text, 'Invalid email or password') !== false);
        }
        //
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
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $response = static::getClient()->request('GET', static::getURL(), ['cookies' => $jar]);
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();

        list($csrfKey, $csrfToken) = static::getCsrfToken($html);

        // try {
        $submitted = static::getClient()->request('post', static::getURL('/auth/login'), [
            'cookies' => $jar,
            'form_params' => [
                $csrfKey => $csrfToken,
                'email' => static::$currentEmail,
                'password' => static::$currentPassword,
            ]
        ]);

        $this->assertTrue(true);

        // TODO: find a better way to test it
        // // $text = $submitted->getBody()->getContents();

        // print_r($submitted->getHeaders());
        // exit;

        // echo "\n\nResponse = " . $submitted->getBody()->getContents() . "\n\n";
        // exit;
        //     $this->assertTrue(false, 'Expected this test fail: expected httpCode 400');
        // } catch (GuzzleHttp\Exception\ClientException $ex) {
        //     $this->assertEquals(400, $ex->getResponse()->getStatusCode());
        //     $text = $ex->getResponse()->getBody()->getContents();
        //     $this->assertTrue(strpos($text, 'Invalid email or password') !== false);
        // }
        //
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
