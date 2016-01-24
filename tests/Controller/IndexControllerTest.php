<?php

namespace Looptribe\Paytoshi\Tests\Controller;


use Looptribe\Paytoshi\Controller\IndexController;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testAction()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $themeProvider = $this->getMock('Looptribe\Paytoshi\Templating\ThemeProviderInterface');
        $templating = $this->getMock('Looptribe\Paytoshi\Templating\TemplatingEngineInterface');
        $settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $flashbag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $captchaProvider = $this->getMock('Looptribe\Paytoshi\Captcha\CaptchaProviderInterface');
        $rewardProvider = $this->getMock('Looptribe\Paytoshi\Logic\RewardProviderInterface');

        $request->method('get')
            ->willReturn('');

        $request->method('getSession')
            ->willReturn($session);

        $session->method('get')
            ->willReturn('');

        $themeProvider->expects($this->once())
            ->method('getTemplate')
            ->with('index.html.twig')
            ->willReturn('default/index.html.twig');

        $templating->expects($this->once())
            ->method('render')
            ->with('default/index.html.twig',
                $this->callback(function($data) {
                    return array_key_exists('referral', $data) &&
                        array_key_exists('address', $data) &&
                        array_key_exists('flashbag', $data) &&
                        array_key_exists('name', $data) &&
                        array_key_exists('description', $data) &&
                        array_key_exists('referral_percentage', $data) &&
                        array_key_exists('rewards', $data) &&
                        array_key_exists('rewards_average', $data) &&
                        array_key_exists('rewards_max', $data) &&
                        array_key_exists('waiting_interval', $data) &&
                        array_key_exists('captcha', $data) &&
                        array_key_exists('provider', $data['captcha']) &&
                        array_key_exists('public_key', $data['captcha']) &&
                        array_key_exists('content', $data) &&
                        array_key_exists('header_box', $data['content']) &&
                        array_key_exists('left_box', $data['content']) &&
                        array_key_exists('right_box', $data['content']) &&
                        array_key_exists('center1_box', $data['content']) &&
                        array_key_exists('center2_box', $data['content']) &&
                        array_key_exists('center3_box', $data['content']) &&
                        array_key_exists('footer_box', $data['content']) &&
                        array_key_exists('theme', $data) &&
                        array_key_exists('name', $data['theme']) &&
                        array_key_exists('css', $data['theme']);
                }
            ))
            ->willReturn(new Response());

        $sut = new IndexController($templating, $themeProvider, $settingsRepository, $flashbag, $captchaProvider, $rewardProvider);
        $response = $sut->action($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }
}
