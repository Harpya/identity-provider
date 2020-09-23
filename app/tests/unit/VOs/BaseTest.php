<?php

use PHPUnit\Framework\TestCase;
use Harpya\IP\VOs\SignupVO;

class BaseTest extends TestCase
{
    public function testInvalidClass()
    {
        // Harpya\IP\Controllers\IndexController
        $this->expectException(\Exception::class);
        $vo = SignupVO::factory(\Harpya\IP\Controllers\IndexController::class, ['email' => 'a', 'password' => 'b']);
    }
}
