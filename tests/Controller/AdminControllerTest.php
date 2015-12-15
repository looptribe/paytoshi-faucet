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
            ->with('default/admin.html.twig');

        $this->sut->action();
    }
}
