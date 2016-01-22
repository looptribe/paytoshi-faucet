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

        $request->method('get')
            ->willReturn('');

        $request->method('getSession')
            ->willReturn($session);

        $session->method('get')
            ->willReturn('');

        $themeProvider->method('getTemplate')
            ->willReturn('default/index.html.twig');

        $templating->expects($this->once())
            ->method('render')
            ->willReturn(new Response());

        $sut = new IndexController($templating, $themeProvider, $settingsRepository, $flashbag);
        $response = $sut->action($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }
}
