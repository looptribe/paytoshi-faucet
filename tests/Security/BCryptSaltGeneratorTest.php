<?php

namespace Looptribe\Paytoshi\Tests\Security;

use Looptribe\Paytoshi\Security\BCryptSaltGenerator;

class BCryptSaltGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $sut = new BCryptSaltGenerator();
        $result = $sut->generate();
        $this->assertStringStartsWith('$2a$13$', $result);
        $this->assertEquals(29, strlen($result));
    }
}
