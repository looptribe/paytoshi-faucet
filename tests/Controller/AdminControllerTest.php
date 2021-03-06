<?php

namespace Looptribe\Paytoshi\Tests\Controller;

use Looptribe\Paytoshi\Controller\AdminController;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var AdminController */
    private $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $templating;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $urlGenerator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $settingsRepository;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $themeProvider;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $paytoshi;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $rewardMapper;

    public function setUp()
    {
        $this->templating = $this->getMock('Looptribe\Paytoshi\Templating\TemplatingEngineInterface');
        $this->urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->themeProvider = $this->getMock('Looptribe\Paytoshi\Templating\ThemeProviderInterface');
        $this->paytoshi = $this->getMock('Looptribe\Paytoshi\Api\PaytoshiApiInterface');
        $this->rewardMapper = $this->getMockBuilder('Looptribe\Paytoshi\Logic\RewardMapper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new AdminController($this->templating, $this->urlGenerator, $this->settingsRepository, $this->themeProvider, $this->paytoshi, $this->rewardMapper, '2.0.0');
    }

    public function testAction1()
    {
        $this->settingsRepository->method('get')
            ->willReturnCallback(function ($params) {
                    if ($params == 'rewards')
                        echo '';
                }
            );

        $this->rewardMapper->method('stringToArray')
            ->willReturn(array());

        $this->templating->expects($this->once())
            ->method('render')
            ->with(
                'admin/admin.html.twig',
                $this->logicalAnd(
                    $this->arrayHasKey('version'),
                    $this->arrayHasKey('db_version'),
                    $this->arrayHasKey('api_key'),
                    $this->arrayHasKey('name'),
                    $this->arrayHasKey('description'),
                    $this->arrayHasKey('theme'),
                    $this->arrayHasKey('captcha_provider'),
                    $this->arrayHasKey('solve_media_challenge_key'),
                    $this->arrayHasKey('solve_media_verification_key'),
                    $this->arrayHasKey('solve_media_authentication_key'),
                    $this->arrayHasKey('recaptcha_public_key'),
                    $this->arrayHasKey('recaptcha_private_key'),
                    $this->arrayHasKey('funcaptcha_public_key'),
                    $this->arrayHasKey('funcaptcha_private_key'),
                    $this->arrayHasKey('waiting_interval'),
                    $this->arrayHasKey('rewards'),
                    $this->arrayHasKey('referral_percentage'),
                    $this->arrayHasKey('custom_css'),
                    $this->arrayHasKey('content_header_box'),
                    $this->arrayHasKey('content_left_box'),
                    $this->arrayHasKey('content_right_box'),
                    $this->arrayHasKey('content_center1_box'),
                    $this->arrayHasKey('content_center2_box'),
                    $this->arrayHasKey('content_center3_box'),
                    $this->arrayHasKey('content_footer_box'),
                    $this->arrayHasKey('theme'),
                    $this->callback(function ($data) {
                        return $data['rewards'] == array();
                    })
                )
            )
            ->willReturn(new Response());

        $balanceResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetBalanceResponse')
            ->disableOriginalConstructor()
            ->getMock();
        $balanceResponse->method('isSuccessful')
            ->willReturn(false);
        $this->paytoshi->method('getBalance')
            ->willReturn($balanceResponse);

        $response = $this->sut->action();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testAction2()
    {
        $this->settingsRepository->method('get')
            ->willReturnCallback(function ($params) {
                if ($params == 'rewards')
                    return '10*100';
                }
            );

        $this->rewardMapper->method('stringToArray')
            ->willReturn(array(
                array('amount' => 10, 'probability' => 100)
            ));

        $this->templating->expects($this->once())
            ->method('render')
            ->with(
                'admin/admin.html.twig',
                $this->logicalAnd(
                    $this->arrayHasKey('version'),
                    $this->arrayHasKey('db_version'),
                    $this->arrayHasKey('api_key'),
                    $this->arrayHasKey('name'),
                    $this->arrayHasKey('description'),
                    $this->arrayHasKey('theme'),
                    $this->arrayHasKey('captcha_provider'),
                    $this->arrayHasKey('solve_media_challenge_key'),
                    $this->arrayHasKey('solve_media_verification_key'),
                    $this->arrayHasKey('solve_media_authentication_key'),
                    $this->arrayHasKey('recaptcha_public_key'),
                    $this->arrayHasKey('recaptcha_private_key'),
                    $this->arrayHasKey('funcaptcha_public_key'),
                    $this->arrayHasKey('funcaptcha_private_key'),
                    $this->arrayHasKey('waiting_interval'),
                    $this->arrayHasKey('rewards'),
                    $this->arrayHasKey('referral_percentage'),
                    $this->arrayHasKey('custom_css'),
                    $this->arrayHasKey('content_header_box'),
                    $this->arrayHasKey('content_left_box'),
                    $this->arrayHasKey('content_right_box'),
                    $this->arrayHasKey('content_center1_box'),
                    $this->arrayHasKey('content_center2_box'),
                    $this->arrayHasKey('content_center3_box'),
                    $this->arrayHasKey('content_footer_box'),
                    $this->arrayHasKey('theme'),
                    $this->callback(function ($data) {
                        return $data['rewards'] == array(
                            array('amount' => 10, 'probability' => 100)
                        );
                    })
                )
            )
            ->willReturn(new Response());

        $balanceResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetBalanceResponse')
            ->disableOriginalConstructor()
            ->getMock();
        $balanceResponse->method('isSuccessful')
            ->willReturn(false);
        $this->paytoshi->method('getBalance')
            ->willReturn($balanceResponse);

        $response = $this->sut->action();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testAction3()
    {
        $this->settingsRepository->method('get')
            ->willReturnCallback(function ($params) {
                if ($params == 'rewards')
                    return '10*100,20*200';
                }
            );

        $this->rewardMapper->method('stringToArray')
            ->willReturn(array(
                array('amount' => 10, 'probability' => 100),
                array('amount' => 20, 'probability' => 200)
            ));

        $this->templating->expects($this->once())
            ->method('render')
            ->with(
                'admin/admin.html.twig',
                $this->logicalAnd(
                    $this->arrayHasKey('version'),
                    $this->arrayHasKey('db_version'),
                    $this->arrayHasKey('api_key'),
                    $this->arrayHasKey('name'),
                    $this->arrayHasKey('description'),
                    $this->arrayHasKey('theme'),
                    $this->arrayHasKey('captcha_provider'),
                    $this->arrayHasKey('solve_media_challenge_key'),
                    $this->arrayHasKey('solve_media_verification_key'),
                    $this->arrayHasKey('solve_media_authentication_key'),
                    $this->arrayHasKey('recaptcha_public_key'),
                    $this->arrayHasKey('recaptcha_private_key'),
                    $this->arrayHasKey('funcaptcha_public_key'),
                    $this->arrayHasKey('funcaptcha_private_key'),
                    $this->arrayHasKey('waiting_interval'),
                    $this->arrayHasKey('rewards'),
                    $this->arrayHasKey('referral_percentage'),
                    $this->arrayHasKey('custom_css'),
                    $this->arrayHasKey('content_header_box'),
                    $this->arrayHasKey('content_left_box'),
                    $this->arrayHasKey('content_right_box'),
                    $this->arrayHasKey('content_center1_box'),
                    $this->arrayHasKey('content_center2_box'),
                    $this->arrayHasKey('content_center3_box'),
                    $this->arrayHasKey('content_footer_box'),
                    $this->arrayHasKey('theme'),
                    $this->callback(function ($data) {
                        return $data['rewards'] == array(
                            array('amount' => 10, 'probability' => 100),
                            array('amount' => 20, 'probability' => 200)
                        );
                    })
                )
            )
            ->willReturn(new Response());

        $balanceResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetBalanceResponse')
            ->disableOriginalConstructor()
            ->getMock();
        $balanceResponse->method('isSuccessful')
            ->willReturn(false);
        $this->paytoshi->method('getBalance')
            ->willReturn($balanceResponse);

        $response = $this->sut->action();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testAction4()
    {
        $this->settingsRepository->method('get')
            ->willReturnCallback(function ($params) {
                if ($params == 'rewards')
                    return '20*200,10*100';
                }
            );

        $this->rewardMapper->method('stringToArray')
            ->willReturn(array(
                array('amount' => 10, 'probability' => 100),
                array('amount' => 20, 'probability' => 200)
            ));

        $this->templating->expects($this->once())
            ->method('render')
            ->with(
                'admin/admin.html.twig',
                $this->logicalAnd(
                    $this->arrayHasKey('version'),
                    $this->arrayHasKey('db_version'),
                    $this->arrayHasKey('api_key'),
                    $this->arrayHasKey('name'),
                    $this->arrayHasKey('description'),
                    $this->arrayHasKey('theme'),
                    $this->arrayHasKey('captcha_provider'),
                    $this->arrayHasKey('solve_media_challenge_key'),
                    $this->arrayHasKey('solve_media_verification_key'),
                    $this->arrayHasKey('solve_media_authentication_key'),
                    $this->arrayHasKey('recaptcha_public_key'),
                    $this->arrayHasKey('recaptcha_private_key'),
                    $this->arrayHasKey('funcaptcha_public_key'),
                    $this->arrayHasKey('funcaptcha_private_key'),
                    $this->arrayHasKey('waiting_interval'),
                    $this->arrayHasKey('rewards'),
                    $this->arrayHasKey('referral_percentage'),
                    $this->arrayHasKey('custom_css'),
                    $this->arrayHasKey('content_header_box'),
                    $this->arrayHasKey('content_left_box'),
                    $this->arrayHasKey('content_right_box'),
                    $this->arrayHasKey('content_center1_box'),
                    $this->arrayHasKey('content_center2_box'),
                    $this->arrayHasKey('content_center3_box'),
                    $this->arrayHasKey('content_footer_box'),
                    $this->arrayHasKey('theme'),
                    $this->callback(function ($data) {
                        return $data['rewards'] == array(
                            array('amount' => 10, 'probability' => 100),
                            array('amount' => 20, 'probability' => 200)
                        );
                    })
                )
            )
            ->willReturn(new Response());

        $balanceResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetBalanceResponse')
            ->disableOriginalConstructor()
            ->getMock();
        $balanceResponse->method('isSuccessful')
            ->willReturn(false);
        $this->paytoshi->method('getBalance')
            ->willReturn($balanceResponse);

        $response = $this->sut->action();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testSaveActionWithEmptyReward()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->request = $parameterBag;

        $this->rewardMapper->method('arrayToString')
            ->with(array())
            ->willReturn('');

        $this->settingsRepository->expects($this->once())
            ->method('setAll')
            ->with(
                $this->logicalAnd(
                    $this->arrayHasKey('rewards'),
                    $this->callback(function ($data) {
                            return $data['rewards'] == '';
                        }
                    )
                )
            );

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('admin')
            ->willReturn('/admin');

        $response = $this->sut->saveAction($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/admin', $response->getTargetUrl());

    }

    public function testSaveActionWithNullReward()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->request = $parameterBag;

        $this->rewardMapper->method('arrayToString')
            ->with(array())
            ->willReturn('');

        $this->settingsRepository->expects($this->once())
            ->method('setAll')
            ->with(
                $this->logicalAnd(
                    $this->arrayHasKey('rewards'),
                    $this->callback(function ($data) {
                        return $data['rewards'] == '';
                    }
                    )
                )
            );

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('admin')
            ->willReturn('/admin');

        $response = $this->sut->saveAction($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/admin', $response->getTargetUrl());

    }

    public function testSaveActionWithSingleReward()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())
            ->method('all')
            ->willReturn(array(
                'rewards' => array(
                    array ('amount' => 10, 'probability' => 100)
                )
            ));
        $request->request = $parameterBag;

        $this->rewardMapper->method('arrayToString')
            ->with(array(
                array ('amount' => 10, 'probability' => 100)
            ))
            ->willReturn('10*100');

        $this->settingsRepository->expects($this->once())
            ->method('setAll')
            ->with(
                $this->logicalAnd(
                    $this->arrayHasKey('rewards'),
                    $this->callback(function ($data) {
                        return $data['rewards'] == '10*100';
                    })
                )
            );

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('admin')
            ->willReturn('/admin');

        $response = $this->sut->saveAction($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/admin', $response->getTargetUrl());

    }

    public function testSaveActionWithMultipleRewards1()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())
            ->method('all')
            ->willReturn(array(
                'rewards' => array(
                    array ('amount' => 10, 'probability' => 100),
                    array ('amount' => 20, 'probability' => 200)
                )
            ));
        $request->request = $parameterBag;

        $this->rewardMapper->method('arrayToString')
            ->with(array(
                array ('amount' => 10, 'probability' => 100),
                array ('amount' => 20, 'probability' => 200)
            ))
            ->willReturn('10*100,20*200');

        $this->settingsRepository->expects($this->once())
            ->method('setAll')
            ->with(
                $this->logicalAnd(
                    $this->arrayHasKey('rewards'),
                    $this->callback(function ($data) {
                        return $data['rewards'] == '10*100,20*200';
                    })
                )
            );

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('admin')
            ->willReturn('/admin');

        $response = $this->sut->saveAction($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/admin', $response->getTargetUrl());

    }

    public function testSaveActionWithMultipleRewards2()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())
            ->method('all')
            ->willReturn(array(
                'rewards' => array(
                    array ('probability' => 100),
                    array ('amount' => 20, 'probability' => 200)
                )
            ));
        $request->request = $parameterBag;

        $this->rewardMapper->method('arrayToString')
            ->with(array(
                array ('probability' => 100),
                array ('amount' => 20, 'probability' => 200)
            ))
            ->willReturn('20*200');

        $this->settingsRepository->expects($this->once())
            ->method('setAll')
            ->with(
                $this->logicalAnd(
                    $this->arrayHasKey('rewards'),
                    $this->callback(function ($data) {
                        return $data['rewards'] == '20*200';
                    })
                )
            );

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('admin')
            ->willReturn('/admin');

        $response = $this->sut->saveAction($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/admin', $response->getTargetUrl());

    }

    public function testSaveActionWithMultipleRewards3()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $parameterBag->expects($this->once())
            ->method('all')
            ->willReturn(array(
                'rewards' => array(
                    array ('amount' => 10),
                    array ('amount' => 20, 'probability' => 200)
                )
            ));
        $request->request = $parameterBag;

        $this->rewardMapper->method('arrayToString')
            ->with(array(
                array ('amount' => 10),
                array ('amount' => 20, 'probability' => 200)
            ))
            ->willReturn('20*200');

        $this->settingsRepository->expects($this->once())
            ->method('setAll')
            ->with(
                $this->logicalAnd(
                    $this->arrayHasKey('rewards'),
                    $this->callback(function ($data) {
                        return $data['rewards'] == '20*200';
                    })
                )
            );

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('admin')
            ->willReturn('/admin');

        $response = $this->sut->saveAction($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/admin', $response->getTargetUrl());

    }

    public function testSaveActionShouldRedirectOnException()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag = new ParameterBag(array('rewards' => array()));
        $request->request = $parameterBag;

        $this->settingsRepository->expects($this->once())
            ->method('setAll')
            ->willThrowException(new \Exception());

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('admin')
            ->willReturn('/admin');

        $response = $this->sut->saveAction($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('/admin', $response->getTargetUrl());

    }
}
