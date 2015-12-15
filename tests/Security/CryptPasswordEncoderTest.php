<?php

namespace Looptribe\Paytoshi\Tests\Security;

use Looptribe\Paytoshi\Security\CryptPasswordEncoder;

class CryptPasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testEncodePassword()
    {
        $sut = new CryptPasswordEncoder();
        $salt = '$2a$13$Fg6IpSnOzkKUtjMNq9PfyS';
        $result = $sut->encodePassword('test', $salt);
        $this->assertEquals('$2a$13$Fg6IpSnOzkKUtjMNq9PfyObr65hdTRNsCk9MicfvfIYP9iGQcXz0C', $result);
    }

    public function testIsPasswordValidTrue()
    {
        $sut = new CryptPasswordEncoder();
        $salt = '$2a$13$Fg6IpSnOzkKUtjMNq9PfyS';
        $encoded = '$2a$13$Fg6IpSnOzkKUtjMNq9PfyObr65hdTRNsCk9MicfvfIYP9iGQcXz0C';
        $result = $sut->isPasswordValid($encoded, 'test', $salt);
        $this->assertTrue($result);
    }

    public function testIsPasswordValidFalse()
    {
        $sut = new CryptPasswordEncoder();
        $salt = '$2a$13$Fg6IpSnOzkKUtjMNq9PfyS';
        $encoded = '$2a$13$Fg6IpSnOzkKUtjMNq9PfyObr65hdTRNsCk9MicfvfIYP9iGQcXz0C';
        $result = $sut->isPasswordValid($encoded, 'test2', $salt);
        $this->assertFalse($result);
    }
}
