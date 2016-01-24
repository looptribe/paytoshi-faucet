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

        $settingsRepository->expects($this->at(0))
            ->method('get')
            ->with('funcaptcha_public_key');

        $settingsRepository->expects($this->at(1))
            ->method('get')
            ->with('funcaptcha_private_key');

        $service = 'funcaptcha';

        $sut = new CaptchaProviderFactory($buzz, $settingsRepository);
        $captchaProvider = $sut->create($service);

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\FunCaptcha\FuncaptchaProvider', $captchaProvider);
    }

    public function testCreate3()
    {
        $buzz = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();

        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $service = 'recaptcha';

        $settingsRepository->expects($this->at(0))
            ->method('get')
            ->with('recaptcha_public_key');

        $settingsRepository->expects($this->at(1))
            ->method('get')
            ->with('recaptcha_private_key');

        $sut = new CaptchaProviderFactory($buzz, $settingsRepository);
        $captchaProvider = $sut->create($service);

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\Recaptcha\RecaptchaProvider', $captchaProvider);
    }

    public function testCreate4()
    {
        $buzz = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();

        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $service = 'solve_media';

        $settingsRepository->expects($this->at(0))
            ->method('get')
            ->with('solve_media_challenge_key');

        $settingsRepository->expects($this->at(1))
            ->method('get')
            ->with('solve_media_verification_key');

        $settingsRepository->expects($this->at(2))
            ->method('get')
            ->with('solve_media_authentication_key');

        $sut = new CaptchaProviderFactory($buzz, $settingsRepository);
        $captchaProvider = $sut->create($service);

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\SolveMedia\SolveMediaProvider', $captchaProvider);
    }

}