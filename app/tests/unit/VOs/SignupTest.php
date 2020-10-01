<?php

namespace unit\VOs;

use PHPUnit\Framework\TestCase;
use Harpya\IP\VOs\SignupVO;

class SignupTest extends TestCase
{
    public function testBindEmpty()
    {
        $vo = SignupVO::factory(SignupVO::class);
        $arr = $vo->toArray();
        $this->assertTrue(is_array($arr));
        $this->assertCount(4, $arr);
        foreach ($arr as $k => $v) {
            $this->assertEmpty($v);
        }
    }

    public function testBindPartial()
    {
        $vo = SignupVO::factory(null, ['email' => 'a', 'password' => 'b']);
        $arr = $vo->toArray();
        $this->assertTrue(is_array($arr));
        $this->assertCount(4, $arr);

        $this->assertEquals('a', $arr['email']);
        $this->assertEquals('a', $vo->get('email'));

        $this->assertEquals('b', $arr['password']);
    }
}
