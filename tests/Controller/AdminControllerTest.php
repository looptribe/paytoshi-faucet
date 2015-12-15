<?php

namespace Looptribe\Paytoshi\Tests\Controller;


use Looptribe\Paytoshi\Controller\AdminController;

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



    public function setUp()
    {
        $this->templating = $this->getMock('Looptribe\Paytoshi\Templating\TemplatingEngineInterface');
        $this->urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->settingsRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\SettingsRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new AdminController($this->templating, $this->urlGenerator, $this->settingsRepository);
    }

    public function testAction()
    {
        $this->templating->expects($this->once())
            ->method('render')
            ->with(
                'default/admin.html.twig',
                $this->logicalAnd(
                    $this->arrayHasKey('version'),
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
                    $this->arrayHasKey('theme')
                )
            );

        $this->sut->action();
    }
}
