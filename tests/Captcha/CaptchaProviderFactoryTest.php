<?php

namespace Looptribe\Paytoshi\Tests\Captcha;

use Looptribe\Paytoshi\Captcha\CaptchaProviderFactory;

class CaptchaProviderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate1()
    {
        $buzz = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();

        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $service = '';

        $sut = new CaptchaProviderFactory($buzz, $settingsRepository);

        $this->setExpectedException('\RuntimeException', 'Invalid captcha provider');
        $captchaProvider = $sut->create($service);
    }

    public function testCreate2()
    {
        $buzz = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();

        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $service = 'funcaptcha';

        $sut = new CaptchaProviderFactory($buzz, $settingsRepository);
        $captchaProvider = $sut->create($service);

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\FunCaptcha\FuncaptchaProvider', $captchaProvider);
    }
}