<?php

namespace Looptribe\Paytoshi\Tests\Controller;

use Looptribe\Paytoshi\Controller\RewardController;

class RewardControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $templating;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $themeProvider;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $request;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $settingsRepository;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $captchaProvider;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $urlGenerator;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $rewardLogic;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $flashBag;

    public function setUp()
    {
        $this->templating = $this->getMock('Looptribe\Paytoshi\Templating\TemplatingEngineInterface');
        $this->themeProvider = $this->getMock('Looptribe\Paytoshi\Templating\ThemeProviderInterface');
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->captchaProvider = $this->getMock('Looptribe\Paytoshi\Captcha\CaptchaProviderInterface');
        $this->urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->rewardLogic = $this->getMockBuilder('Looptribe\Paytoshi\Logic\RewardLogic')
            ->disableOriginalConstructor()
            ->getMock();
        $this->flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
    }

    public function testAction1()
    {
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('homepage')
            ->willReturn('/');

        $this->flashBag->expects($this->once())
            ->method('add')
            ->with('warning', 'Missing address');

        $sut = new RewardController($this->templating, $this->themeProvider, $this->settingsRepository, $this->captchaProvider, $this->urlGenerator, $this->rewardLogic, $this->flashBag);
        $response = $sut->action($this->request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }

    public function testAction2()
    {
        $this->request->method('get')
            ->willReturnCallback(function ($param) {
                switch($param) {
                    case 'address':
                        return 'addr1';
                }
            });

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('homepage')
            ->willReturn('/');

        $this->captchaProvider->method('getChallengeName')
            ->willReturn('challenge');

        $this->flashBag->expects($this->once())
            ->method('add')
            ->with('warning', 'Missing captcha');

        $sut = new RewardController($this->templating, $this->themeProvider, $this->settingsRepository, $this->captchaProvider, $this->urlGenerator, $this->rewardLogic, $this->flashBag);
        $response = $sut->action($this->request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }

    public function testAction3()
    {
        $this->request->method('get')
            ->willReturnCallback(function ($param) {
                switch($param) {
                    case 'address':
                        return 'addr1';
                }
            });

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('homepage')
            ->willReturn('/');

        $this->captchaProvider->expects($this->once())
            ->method('getChallengeName')
            ->willReturn(false);

        $this->captchaProvider->method('getResponseName')
            ->willReturn('response');

        $this->flashBag->expects($this->once())
            ->method('add')
            ->with('warning', 'Missing captcha');

        $sut = new RewardController($this->templating, $this->themeProvider, $this->settingsRepository, $this->captchaProvider, $this->urlGenerator, $this->rewardLogic, $this->flashBag);
        $response = $sut->action($this->request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }

    public function testAction4()
    {
        $result = $this->getMockBuilder('Looptribe\Paytoshi\Logic\RewardLogicResult')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request->method('get')
            ->willReturnCallback(function ($param) {
                switch($param) {
                    case 'address':
                        return 'addr1';
                    case 'response':
                        return 'response';
                }
            });

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('homepage')
            ->willReturn('/');

        $this->captchaProvider->expects($this->once())
            ->method('getChallengeName')
            ->willReturn(false);

        $this->captchaProvider->method('getResponseName')
            ->willReturn('response');

        $this->request->expects($this->once())
            ->method('getClientIp')
            ->willReturn('10.10.10.10');

        $this->rewardLogic->expects($this->once())
            ->method('create')
            ->with('addr1', '10.10.10.10', null, 'response', null)
            ->willReturn($result);

        $result->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $result->expects($this->once())
            ->method('getSeverity')
            ->willReturn('danger');

        $result->expects($this->once())
            ->method('getError')
            ->willReturn('Error');

        $this->flashBag->expects($this->once())
            ->method('add')
            ->with('danger', 'Error');

        $sut = new RewardController($this->templating, $this->themeProvider, $this->settingsRepository, $this->captchaProvider, $this->urlGenerator, $this->rewardLogic, $this->flashBag);
        $response = $sut->action($this->request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }

    public function testAction5()
    {
        $result = $this->getMockBuilder('Looptribe\Paytoshi\Logic\RewardLogicResult')
            ->disableOriginalConstructor()
            ->getMock();

        $ApiResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request->method('get')
            ->willReturnCallback(function ($param) {
                switch($param) {
                    case 'address':
                        return 'addr1';
                    case 'challenge':
                        return 'challenge';
                    case 'response':
                        return 'response';
                }
            });

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('homepage')
            ->willReturn('/');

        $this->captchaProvider->method('getChallengeName')
            ->willReturn('challenge');

        $this->captchaProvider->method('getResponseName')
            ->willReturn('response');

        $this->request->expects($this->once())
            ->method('getClientIp')
            ->willReturn('10.10.10.10');

        $this->rewardLogic->expects($this->once())
            ->method('create')
            ->with('addr1', '10.10.10.10', 'challenge', 'response', null)
            ->willReturn($result);

        $result->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $result->expects($this->exactly(2))
            ->method('getResponse')
            ->willReturn($ApiResponse);

        $ApiResponse->expects($this->once())
            ->method('getAmount')
            ->willReturn(10);

        $ApiResponse->expects($this->once())
            ->method('getRecipient')
            ->willReturn('addr1');

        $this->themeProvider->expects($this->once())
            ->method('getTemplate')
            ->with('balance.html.twig')
            ->willReturn('template');

        $this->templating->expects($this->once())
            ->method('renderView')
            ->with('template', array('amount' => 10, 'address' => 'addr1'))
            ->willReturn('balance_template');

        $this->flashBag->expects($this->once())
            ->method('add')
            ->with('success', 'balance_template');

        $sut = new RewardController($this->templating, $this->themeProvider, $this->settingsRepository, $this->captchaProvider, $this->urlGenerator, $this->rewardLogic, $this->flashBag);
        $response = $sut->action($this->request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }
}