<?php

namespace Looptribe\Paytoshi\Tests\Controller;


use Looptribe\Paytoshi\Controller\IndexController;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testAction()
    {
        $templating = $this->getMock('Looptribe\Paytoshi\Templating\TemplatingEngineInterface');
        $themeProvider = $this->getMock('Looptribe\Paytoshi\Templating\ThemeProviderInterface');

        $themeProvider->method('getTemplate')
            ->willReturn('default/index.html.twig');

        $templating->expects($this->once())
            ->method('render')
            ->willReturn(new Response());

        $sut = new IndexController($templating, $themeProvider);
        $response = $sut->action();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }
}
