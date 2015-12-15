<?php

namespace Looptribe\Paytoshi\Tests\Security;

use Looptribe\Paytoshi\Security\AlphaNumericPasswordGenerator;

class AlphaNumericPasswordGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $sut = new AlphaNumericPasswordGenerator();
        $result = $sut->generate();

        $this->assertEquals(16, strlen($result));
    }
}
