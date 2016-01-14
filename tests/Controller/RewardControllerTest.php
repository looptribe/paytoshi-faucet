<?php

namespace Looptribe\Paytoshi\Tests\Controller;

use Looptribe\Paytoshi\Controller\RewardController;

class RewardControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testAction()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $captchaProvider = $this->getMock('Looptribe\Paytoshi\Captcha\CaptchaProviderInterface');
        $urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $rewardLogic = $this->getMockBuilder('Looptribe\Paytoshi\Logic\RewardLogic')
            ->disableOriginalConstructor()
            ->getMock();
        $flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');

        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('homepage')
            ->willReturn('/');

        $sut = new RewardController($settingsRepository, $captchaProvider, $urlGenerator, $rewardLogic, $flashBag);
        $response = $sut->action($request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }
}