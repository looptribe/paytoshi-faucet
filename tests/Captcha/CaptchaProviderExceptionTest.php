<?php

namespace Looptribe\Paytoshi\Tests\Captcha;

use Looptribe\Paytoshi\Captcha\CaptchaProviderException;

/**
 * Created by PhpStorm.
 * User: Diego
 * Date: 12/01/2016
 * Time: 14.46
 */
class CaptchaProviderExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $sut = new CaptchaProviderException();
        $this->assertInstanceOf('\Exception', $sut);
    }
}
