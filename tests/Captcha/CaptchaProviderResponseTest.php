<?php

namespace Looptribe\Paytoshi\Tests\Captcha;

use Looptribe\Paytoshi\Captcha\CaptchaProviderResponse;

class CaptchaProviderResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor1()
    {
        $success = true;
        $sut = new CaptchaProviderResponse($success);
        $this->assertTrue($sut->isSuccessful());
    }

    public function testConstructor2()
    {
        $success = false;
        $sut = new CaptchaProviderResponse($success);
        $this->assertFalse($sut->isSuccessful());
    }

    public function testConstructor3()
    {
        $success = false;
        $message = 'Invalid captcha';
        $sut = new CaptchaProviderResponse($success, $message);
        $this->assertFalse($sut->isSuccessful());
        $this->assertEquals($message, $sut->getMessage());
    }
}
